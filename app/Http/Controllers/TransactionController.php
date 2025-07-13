<?php
namespace App\Http\Controllers;

use App\Models\Inbox;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\Voucher;
use App\Mail\VoucherSentMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Midtrans\Config;
use Midtrans\Snap;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification as FcmNotification;

class TransactionController extends Controller
{
    public function __construct()
    {
        // Set konfigurasi Midtrans
        Config::$serverKey = config('services.midtrans.server_key');
        Config::$isProduction = config('services.midtrans.is_production');
        Config::$isSanitized = config('services.midtrans.is_sanitized');
        Config::$is3ds = config('services.midtrans.is_3ds');
    }

    public function checkout(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'game_user_id' => 'required|string|max:100',
            'zone_id' => 'nullable|string|max:50',
            'customer_email' => 'required|email',
            'customer_phone' => 'required|string|max:20',
        ]);

        $product = Product::findOrFail($request->product_id);
        $order_id = 'ORD-' . time() . '-' . uniqid();

        $transaction = Transaction::create([
            'order_id' => $order_id,
            'user_id' => Auth::check() ? Auth::id() : null,
            'product_id' => $product->id,
            'game_user_id' => $request->game_user_id,
            'zone_id' => $request->zone_id,
            'customer_email' => $request->customer_email,
            'customer_phone' => $request->customer_phone,
            'total_price' => $product->price,
            'status' => 'pending',
        ]);

        $midtrans_params = [
            'transaction_details' => [
                'order_id' => $transaction->order_id,
                'gross_amount' => $transaction->total_price,
            ],
            'item_details' => [[
                'id' => $product->id,
                'price' => $product->price,
                'quantity' => 1,
                'name' => $product->name . ' - ' . $product->game->name,
            ]],
            'customer_details' => [
                'first_name' => Auth::check() ? Auth::user()->name : 'Guest',
                'email' => $request->customer_email,
                'phone' => $request->customer_phone,
            ]
        ];

        try {
            $snapToken = Snap::getSnapToken($midtrans_params);
            $transaction->snap_token = $snapToken;
            $transaction->save();
            return response()->json(['snap_token' => $snapToken]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function show($order_id)
    {
        $transaction = Transaction::where('order_id', $order_id)->firstOrFail();
        return view('pages.transaction-show', compact('transaction'));
    }

    private function sendVoucher(Transaction $transaction)
    {
        // Cari voucher yang tersedia untuk produk ini
        $voucher = Voucher::where('product_id', $transaction->product_id)
                          ->where('is_used', false)
                          ->first();

        if (!$voucher) {
            Log::error('Stok voucher habis untuk produk ID: ' . $transaction->product_id);
            return;
        }

        DB::transaction(function () use ($transaction, $voucher) {
            $transaction->voucher_code = $voucher->code;
            $transaction->save();

            $voucher->is_used = true;
            $voucher->save();

            // Kirim email ke pelanggan berdasarkan email yang diisi di form
            Mail::to($transaction->customer_email)->send(new VoucherSentMail($transaction));

            // Jika pelanggan adalah user terdaftar, kirim notifikasi tambahan
            if ($transaction->user) {
                Inbox::create([
                    'user_id' => $transaction->user_id,
                    'title' => 'Voucher Anda Telah Dikirim!',
                    'body' => 'Kode voucher untuk pesanan ' . $transaction->order_id . ' adalah: ' . $transaction->voucher_code,
                    'type' => 'voucher',
                    'link' => route('transaction.show', $transaction->order_id),
                ]);

                if ($transaction->user->fcm_token) {
                    try {
                        $messaging = app('firebase.messaging');
                        $notification = FcmNotification::create('Voucher Terkirim!', 'Kode voucher untuk pesanan ' . $transaction->order_id . ' telah dikirim.');
                        $message = CloudMessage::withTarget('token', $transaction->user->fcm_token)->withNotification($notification);
                        $messaging->send($message);
                    } catch (\Exception $e) {
                        Log::error('Gagal mengirim FCM: ' . $e->getMessage());
                    }
                }
            }
        });
    }

    public function callback(Request $request)
    {
        $serverKey = config('services.midtrans.server_key');
        $hashed = hash("sha512", $request->order_id . $request->status_code . $request->gross_amount . $serverKey);

        if ($hashed == $request->signature_key) {
            $transaction = Transaction::with('user', 'product')->where('order_id', $request->order_id)->first();

            if (!$transaction) {
                return response()->json(['message' => 'Transaction not found.'], 200);
            }

            // Hanya proses jika statusnya belum sukses
            if ($transaction->status === 'pending') {
                if ($request->transaction_status == 'capture' || $request->transaction_status == 'settlement') {
                    $transaction->status = 'success';
                    $transaction->payment_method = $request->payment_type;
                    $transaction->save();

                    // Panggil metode untuk mengirim voucher
                    $this->sendVoucher($transaction);
                } elseif ($request->transaction_status == 'expire') {
                    $transaction->status = 'expired';
                    $transaction->save();
                } elseif ($request->transaction_status == 'deny') {
                    $transaction->status = 'failed';
                    $transaction->save();
                }
            }
        }

        return response()->json(['message' => 'Notification handled.'], 200);
    }

        public function retryPayment(Request $request, Transaction $transaction)
    {
        // Pastikan hanya pemilik transaksi yang bisa mencoba lagi
        if ($transaction->user_id !== Auth::id()) {
            abort(403);
        }

        // Pastikan statusnya memungkinkan untuk dicoba lagi
        if (!in_array($transaction->status, ['pending', 'failed', 'expired'])) {
            return response()->json(['error' => 'Transaksi ini tidak dapat dibayar ulang.'], 400);
        }

        // Buat parameter untuk Midtrans
        $midtrans_params = [
            'transaction_details' => [
                'order_id' => $transaction->order_id,
                'gross_amount' => $transaction->total_price,
            ],
            'item_details' => [[
                'id' => $transaction->product->id,
                'price' => $transaction->total_price,
                'quantity' => 1,
                'name' => $transaction->product->name . ' - ' . $transaction->product->game->name,
            ]],
            'customer_details' => [
                'first_name' => $transaction->user->name ?? 'Guest',
                'email' => $transaction->customer_email,
                'phone' => $transaction->customer_phone,
            ]
        ];

        try {
            $snapToken = Snap::getSnapToken($midtrans_params);
            $transaction->snap_token = $snapToken;
            $transaction->save();
            return response()->json(['snap_token' => $snapToken]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
<?php
namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Midtrans\Config;
use Midtrans\Snap;

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

    public function callback(Request $request)
    {
        $serverKey = config('services.midtrans.server_key');
        $hashed = hash("sha512", $request->order_id . $request->status_code . $request->gross_amount . $serverKey);

        if ($hashed == $request->signature_key) {
            $transaction = Transaction::where('order_id', $request->order_id)->first();
            
            if (!$transaction) {
                // Jika transaksi tidak ditemukan, kirim respons OK agar Midtrans berhenti mengirim notifikasi
                // Ini penting untuk menangani notifikasi tes dari dashboard Midtrans
                return response()->json(['message' => 'Transaction not found.'], 200);
            }
            
            if ($request->transaction_status == 'capture' || $request->transaction_status == 'settlement') {
                $transaction->status = 'success';
            } elseif ($request->transaction_status == 'pending') {
                $transaction->status = 'pending';
            } else {
                $transaction->status = 'failed';
            }
            $transaction->payment_method = $request->payment_type;
            $transaction->save();
        }
    }
}
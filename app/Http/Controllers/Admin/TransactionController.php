<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Models\Voucher; // <-- Tambahkan ini
use App\Mail\VoucherSentMail; // <-- Tambahkan ini
use Illuminate\Support\Facades\DB; // <-- Tambahkan ini
use Illuminate\Support\Facades\Mail; // <-- Tambahkan ini
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;
use App\Models\Inbox;

class TransactionController extends Controller
{
    public function index(Request $request)
    {
        // Query dasar untuk transaksi
        $query = Transaction::with(['product.game', 'user'])->latest();

        // Terapkan filter jika ada
        if ($request->filled('status') && $request->status != 'all') {
            $query->where('status', $request->status);
        }

        $transactions = $query->paginate(15)->withQueryString();

        return view('admin.transactions.index', compact('transactions'));
    }

    public function show(Transaction $transaction)
    {
        $transaction->load(['product.game', 'user']);
        return view('admin.transactions.show', compact('transaction'));
    }

public function sendVoucher(Request $request, Transaction $transaction)
    {
        if ($transaction->status != 'success' || $transaction->voucher_code != null) {
            return back()->with('error', 'Voucher tidak dapat dikirim untuk transaksi ini.');
        }

        $voucher = Voucher::where('product_id', $transaction->product_id)
                          ->where('is_used', false)
                          ->first();

        if (!$voucher) {
            return back()->with('error', 'Stok voucher untuk produk ini habis.');
        }

        DB::transaction(function () use ($transaction, $voucher) {
            $transaction->voucher_code = $voucher->code;
            $transaction->save();

            $voucher->is_used = true;
            $voucher->save();

            if ($transaction->user) {
                // Kirim Email
                Mail::to($transaction->user->email)->send(new VoucherSentMail($transaction));

                // Buat pesan di Kotak Masuk
                Inbox::create([
                    'user_id' => $transaction->user_id,
                    'title' => 'Voucher Anda Telah Dikirim!',
                    'body' => 'Kode voucher untuk pesanan ' . $transaction->order_id . ' adalah: ' . $transaction->voucher_code,
                    'type' => 'voucher',
                    'link' => route('transaction.show', $transaction->order_id),
                ]);


                // Kirim Notifikasi FCM jika user memiliki token
                if ($transaction->user->fcm_token) {
                    $messaging = app('firebase.messaging');
                    $notification = Notification::create(
                        'Voucher Terkirim!',
                        'Kode voucher untuk pesanan ' . $transaction->order_id . ' telah dikirim.'
                    );
                    $message = CloudMessage::withTarget('token', $transaction->user->fcm_token)
                        ->withNotification($notification);
                    
                    $messaging->send($message);
                }
            }
        });

        return back()->with('success', 'Kode voucher berhasil dikirim ke pelanggan.');
    }
}

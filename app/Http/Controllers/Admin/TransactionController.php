<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Inbox;
use App\Models\Transaction;
use App\Models\Voucher;
use App\Mail\VoucherSentMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification as FcmNotification;

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
        // Pastikan transaksi sukses dan belum pernah dikirim voucher
        if ($transaction->status != 'success' || $transaction->voucher_code != null) {
            return back()->with('error', 'Voucher tidak dapat dikirim untuk transaksi ini.');
        }

        // Cari voucher yang tersedia untuk produk ini
        $voucher = Voucher::where('product_id', $transaction->product_id)
                          ->where('is_used', false)
                          ->first();

        if (!$voucher) {
            return back()->with('error', 'Stok voucher untuk produk ini habis. Silakan tambah stok terlebih dahulu.');
        }

        // Gunakan transaksi database untuk memastikan konsistensi data
        DB::transaction(function () use ($transaction, $voucher) {
            // Update transaksi dengan kode voucher
            $transaction->voucher_code = $voucher->code;
            $transaction->save();

            // Tandai voucher sebagai sudah digunakan
            $voucher->is_used = true;
            $voucher->save();

            // Kirim email ke pelanggan berdasarkan email yang diisi di form
            Mail::to($transaction->customer_email)->send(new VoucherSentMail($transaction));

            // Jika pelanggan adalah user terdaftar, kirim notifikasi tambahan
            if ($transaction->user) {
                // Buat pesan di Kotak Masuk
                Inbox::create([
                    'user_id' => $transaction->user_id,
                    'title' => 'Voucher Anda Telah Dikirim! (Manual)',
                    'body' => 'Kode voucher untuk pesanan ' . $transaction->order_id . ' adalah: ' . $transaction->voucher_code,
                    'type' => 'voucher',
                    'link' => route('transaction.show', $transaction->order_id),
                ]);

                // Kirim Notifikasi FCM jika user memiliki token
                if ($transaction->user->fcm_token) {
                    try {
                        $messaging = app('firebase.messaging');
                        $notification = FcmNotification::create(
                            'Voucher Terkirim!',
                            'Kode voucher untuk pesanan ' . $transaction->order_id . ' telah dikirim.'
                        );
                        $message = CloudMessage::withTarget('token', $transaction->user->fcm_token)
                            ->withNotification($notification);
                        
                        $messaging->send($message);
                    } catch (\Exception $e) {
                        Log::error('Gagal mengirim FCM saat kirim ulang: ' . $e->getMessage());
                    }
                }
            }
        });

        return back()->with('success', 'Kode voucher berhasil dikirim ulang ke pelanggan.');
    }
}

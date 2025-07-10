<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Models\Voucher; // <-- Tambahkan ini
use App\Mail\VoucherSentMail; // <-- Tambahkan ini
use Illuminate\Support\Facades\DB; // <-- Tambahkan ini
use Illuminate\Support\Facades\Mail; // <-- Tambahkan ini

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

            // Kirim email ke pengguna jika pengguna terdaftar
            if ($transaction->user) {
                Mail::to($transaction->user->email)->send(new VoucherSentMail($transaction));
            }
        });

        return back()->with('success', 'Kode voucher berhasil dikirim ke pelanggan.');
    }
}

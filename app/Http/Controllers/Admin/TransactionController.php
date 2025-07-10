<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use Illuminate\Http\Request;

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
}

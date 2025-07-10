<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $totalRevenue = Transaction::where('status', 'success')->sum('total_price');
        $successfulTransactions = Transaction::where('status', 'success')->count();
        $pendingTransactions = Transaction::where('status', 'pending')->count();
        $latestTransactions = Transaction::with('product.game', 'user')->latest()->take(5)->get();

        return view('admin.dashboard', compact('totalRevenue', 'successfulTransactions', 'pendingTransactions', 'latestTransactions'));
    }
}

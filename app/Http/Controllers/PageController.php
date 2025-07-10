<?php
namespace App\Http\Controllers;

use App\Models\Game;
use App\Models\Transaction;
use Illuminate\Http\Request;

class PageController extends Controller
{
    public function home()
    {
        $games = Game::where('is_active', true)->latest()->get();
        return view('pages.home', compact('games'));
    }

    public function topupForm(Game $game)
    {
        $products = $game->products()->where('is_active', true)->get();
        return view('pages.topup-form', compact('game', 'products'));
    }

    public function myTransactions()
    {
        // Ganti ->get() menjadi ->paginate()
        $transactions = Transaction::where('user_id', auth()->id())->latest()->paginate(10);
        return view('pages.my-transactions', compact('transactions'));
    }
}
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PageController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\Admin\BannerController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\GameController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\TransactionController as AdminTransactionController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\VoucherController;

// Halaman Utama (Browse Games)
Route::get('/', [PageController::class, 'home'])->name('home');

// Halaman Form Top-up
Route::get('/top-up/{game:slug}', [PageController::class, 'topupForm'])->name('topup.form');

// Halaman Histori Transaksi Pengguna (memerlukan login)
Route::middleware('auth')->get('/my-transactions', [PageController::class, 'myTransactions'])->name('my.transactions');

// Proses Checkout (membuat transaksi & mendapatkan Snap Token)
Route::post('/checkout', [TransactionController::class, 'checkout'])->name('checkout');

// Halaman Detail Transaksi setelah pembayaran
Route::get('/transaction/{order_id}', [TransactionController::class, 'show'])->name('transaction.show');

// Callback dari Midtrans (JANGAN DIBERI MIDDLEWARE WEB)
Route::post('/midtrans/callback', [TransactionController::class, 'callback']);


Route::prefix('admin')->name('admin.')->middleware(['auth', 'admin'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    Route::resource('games', GameController::class);
    Route::resource('products', ProductController::class);
    Route::resource('users', UserController::class)->only(['index', 'destroy']);
    Route::resource('banners', BannerController::class);
    Route::resource('transactions', AdminTransactionController::class)->only(['index', 'show']);
    Route::resource('vouchers', VoucherController::class)->except(['show']);
    Route::post('transactions/{transaction}/send-voucher', [AdminTransactionController::class, 'sendVoucher'])->name('transactions.sendVoucher');

});


// Rute bawaan Breeze
require __DIR__.'/auth.php';

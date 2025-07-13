<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PageController;
use App\Http\Controllers\InboxController;
use App\Http\Controllers\FcmController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\BannerController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\GameController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\PromoNotificationController;
use App\Http\Controllers\Admin\TransactionController as AdminTransactionController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\VoucherController;

// == RUTE PUBLIK & PENGGUNA ==
Route::get('/', [PageController::class, 'home'])->name('home');
Route::get('/top-up/{game:slug}', [PageController::class, 'topupForm'])->name('topup.form');
Route::get('/transaction/{order_id}', [TransactionController::class, 'show'])->name('transaction.show');
Route::post('/checkout', [TransactionController::class, 'checkout'])->name('checkout');

// Rute yang memerlukan login
Route::middleware('auth')->group(function () {
    Route::get('/my-transactions', [PageController::class, 'myTransactions'])->name('my.transactions');
    Route::get('/inbox', [InboxController::class, 'index'])->name('inbox.index');
    Route::post('/fcm-token', [FcmController::class, 'saveToken'])->name('fcm.token');

    // Rute Profil Pengguna
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Rute untuk coba bayar ulang
    Route::post('/transaction/{transaction}/retry', [TransactionController::class, 'retryPayment'])->name('transaction.retry');
});

// Callback dari Midtrans (tanpa middleware 'web')
Route::post('/midtrans/callback', [TransactionController::class, 'callback']);

// Rute bawaan Laravel Breeze untuk autentikasi (login, register, dll.)
require __DIR__.'/auth.php';


// == RUTE PANEL ADMIN ==
Route::prefix('admin')
    ->name('admin.')
    ->middleware(['auth', 'admin'])
    ->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        // CRUD Resources
        Route::resource('games', GameController::class);
        Route::resource('products', ProductController::class);
        Route::resource('banners', BannerController::class);
        Route::resource('vouchers', VoucherController::class)->except(['show', 'edit', 'update']);
        Route::resource('users', UserController::class)->only(['index', 'destroy']);
        Route::resource('transactions', AdminTransactionController::class)->only(['index', 'show']);

        // Aksi spesifik
        Route::post('transactions/{transaction}/send-voucher', [AdminTransactionController::class, 'sendVoucher'])->name('transactions.sendVoucher');
        
        // Notifikasi Promo
        Route::get('promo-notifications/create', [PromoNotificationController::class, 'create'])->name('promo-notifications.create');
        Route::post('promo-notifications/send', [PromoNotificationController::class, 'send'])->name('promo-notifications.send');
    });
<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Banner; // <-- Tambahkan ini
use Illuminate\Support\Facades\View; // <-- Tambahkan ini

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        try {
            $banners = Banner::where('is_active', true)->latest()->get();
            View::share('banners', $banners);
        } catch (\Exception $e) {
            // Jika tabel belum ada (misal saat migrasi), bagikan koleksi kosong
            View::share('banners', collect());
        }
    }
}

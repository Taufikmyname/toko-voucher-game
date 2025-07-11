<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Banner; // <-- Tambahkan ini
use Illuminate\Support\Facades\View; // <-- Tambahkan ini
use App\Models\Inbox;
use Illuminate\Support\Facades\Auth;


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
        // Bagikan data banner dan notifikasi ke semua view
        View::composer('*', function ($view) {
            try {
                $banners = Banner::where('is_active', true)->latest()->get();
                View::share('banners', $banners);
                
                if (Auth::check()) {
                    $unreadMessages = Inbox::where('user_id', Auth::id())->where('is_read', false)->count();
                    $view->with('unreadMessages', $unreadMessages);
                } else {
                    $view->with('unreadMessages', 0);
                }

            } catch (\Exception $e) {
                $view->with('banners', collect());
                $view->with('unreadMessages', 0);
            }
        });
    }
}

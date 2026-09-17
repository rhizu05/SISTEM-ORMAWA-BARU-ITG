<?php

namespace App\Providers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

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
        // UI-011: jumlah notifikasi belum dibaca untuk badge di sidebar/nav.
        View::composer(['layouts.sidebar', 'layouts.navigation'], function ($view) {
            $user = Auth::user();

            $view->with(
                'unreadNotifikasi',
                $user ? $user->notifikasi()->where('status_baca', 'belum')->count() : 0
            );
        });
    }
}

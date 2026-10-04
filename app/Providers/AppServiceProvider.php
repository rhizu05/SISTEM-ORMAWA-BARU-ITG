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
        \Carbon\Carbon::setLocale('id');

        if ($this->app->environment('production')) {
            \Illuminate\Support\Facades\URL::forceScheme('https');
        }

        // Rate Limiter anti-spam untuk formulir tiket layanan publik mahasiswa
        \Illuminate\Support\Facades\RateLimiter::for('layanan-publik', function (\Illuminate\Http\Request $request) {
            return \Illuminate\Cache\RateLimiting\Limit::perMinute(10)->by($request->ip());
        });

        // Rate Limiter anti-brute force untuk pelacakan tiket layanan publik (maks 10 percobaan/menit per IP)
        \Illuminate\Support\Facades\RateLimiter::for('layanan-tracking', function (\Illuminate\Http\Request $request) {
            return \Illuminate\Cache\RateLimiting\Limit::perMinute(10)->by($request->ip());
        });

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

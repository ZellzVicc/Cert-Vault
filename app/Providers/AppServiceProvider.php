<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL; // <--- Tambahkan Baris Ini

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
        // --- TAMBAHKAN BLOK KODE INI ---
        // Jika aplikasi mendeteksi sedang berjalan di Ngrok atau HTTPS
        if (str_contains(config('app.url'), 'ngrok') || request()->header('X-Forwarded-Proto') === 'https') {
            URL::forceScheme('https');
        }
        // -------------------------------
    }
}

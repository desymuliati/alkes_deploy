<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\URL; // <-- Tambahkan baris ini

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // Daftarkan custom Blade components
        Blade::component('components.admin-layout', 'admin-layout');
        Blade::component('components.user-layout', 'user-layout');

        // <-- Tambahkan blok kode ini di sini
        // Force HTTPS scheme in production environment, especially for Vercel deployment
        if (config('app.env') === 'production' && str_contains(config('app.url'), 'vercel.app')) {
            URL::forceScheme('https');
        }
        // --> Akhir blok kode yang ditambahkan
    }
}
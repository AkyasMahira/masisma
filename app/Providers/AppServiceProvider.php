<?php

namespace App\Providers;

use Illuminate\Pagination\Paginator;
use Illuminate\Support\ServiceProvider;

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
        // Laravel 8+ default pagination = Tailwind; aplikasi ini memakai Bootstrap (seperti Laravel 7)
        Paginator::useBootstrapFour();

        // Akun Gmail pengirim yang diatur admin (menu Pengaturan Email) menimpa MAIL_* di .env
        try { \App\Support\PengaturanEmail::terapkan(); } catch (\Throwable $e) { /* pakai .env */ }
    }
}

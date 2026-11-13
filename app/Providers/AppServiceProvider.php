<?php

namespace App\Providers;

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
        // Terapkan ulang middleware hak akses (access:kunci) pada setiap aksi Livewire,
        // sehingga perubahan di Admin → Hak Akses langsung berlaku tanpa menunggu muat ulang halaman.
        \Livewire\Livewire::addPersistentMiddleware([
            \App\Http\Middleware\AccessMiddleware::class,
        ]);
    }
}

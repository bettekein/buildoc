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
        // Allow Super Admin to access Log Viewer
        \Illuminate\Support\Facades\Gate::define('viewLogViewer', function ($user) {
            return $user->hasRole('Super Admin');
        });
    }
}

<?php

namespace App\Providers;
use App\Models\Creator;
use Illuminate\Support\Facades\Auth;
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
        Auth::provider('creators', function($app, array $config) {
            return new EloquentUserProvider($app['hash'], Creator::class);
        });
    }
}

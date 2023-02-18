<?php

namespace App\Providers;

use App\Contracts\AuthServiceInterface;
use App\Services\AuthService;
use Illuminate\Support\ServiceProvider;

class CustomAuthServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(
            AuthServiceInterface::class,
            AuthService::class
        );
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}

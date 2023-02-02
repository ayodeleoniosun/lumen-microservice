<?php

namespace App\Providers;

use App\Contracts\OauthServiceInterface;
use App\Services\OauthService;
use Illuminate\Support\ServiceProvider;

class OauthServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register(): void
    {
        $this->app->bind(
            OauthServiceInterface::class,
            OauthService::class
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

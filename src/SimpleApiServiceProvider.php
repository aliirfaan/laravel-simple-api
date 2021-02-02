<?php

namespace aliirfaan\LaravelSimpleApi;

use aliirfaan\LaravelSimpleApi\Services\ApiHelperService;

class SimpleApiServiceProvider extends \Illuminate\Support\ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('aliirfaan\LaravelSimpleApi\Services\ApiHelperService', function ($app) {
            return new ApiHelperService();
        });
    }

    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
    }
}

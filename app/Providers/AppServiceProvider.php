<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Modules\Shared\Contracts\ProductServiceInterface;
use Modules\Products\Services\ProductService;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(
            ProductServiceInterface::class,
            ProductService::class
        );
    }

    public function boot(): void
    {
        //
    }
}

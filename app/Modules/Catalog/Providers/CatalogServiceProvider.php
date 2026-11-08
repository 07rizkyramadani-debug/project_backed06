<?php

namespace Modules\Catalog\Providers;

use Illuminate\Support\ServiceProvider;

class CatalogServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        // Memuat migrasi dan route milik bounded context ini
        $this->loadMigrationsFrom(__DIR__ . '/../Migrations');
        $this->loadRoutesFrom(__DIR__ . '/../routes/web.php');
    }
}

<?php

namespace App\Providers;

use App\Models\Purchase;
use App\Observers\PurchaseObserver;
use App\Repositories\PurchaseRepository;
use App\Services\PurchaseService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Bind de repositorios
        $this->app->singleton(PurchaseRepository::class, function ($app) {
            return new PurchaseRepository();
        });

        // Bind de servicios
        $this->app->singleton(PurchaseService::class, function ($app) {
            return new PurchaseService(
                $app->make(PurchaseRepository::class)
            );
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Registrar el Observer
        Purchase::observe(PurchaseObserver::class);
    }
}

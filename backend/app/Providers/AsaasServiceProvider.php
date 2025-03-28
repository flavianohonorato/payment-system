<?php

namespace App\Providers;

use App\Services\Asaas\AsaasClient;
use App\Services\Asaas\AsaasService;
use App\Services\Asaas\Contracts\AsaasServiceInterface;
use Illuminate\Support\ServiceProvider;

class AsaasServiceProvider extends ServiceProvider
{
    /**
     * @return void
     */
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__.'/../../config/asaas.php', 'asaas'
        );

        $this->app->singleton(AsaasClient::class, function ($app) {
            return new AsaasClient();
        });

        $this->app->singleton(AsaasServiceInterface::class, function ($app) {
            return new AsaasService($app->make(AsaasClient::class));
        });
    }

    /**
     * @return void
     */
    public function boot(): void
    {
        $this->publishes([
            __DIR__.'/../../config/asaas.php' => config_path('asaas.php'),
        ], 'asaas-config');
    }
}

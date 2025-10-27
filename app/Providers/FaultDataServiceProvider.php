<?php

namespace App\Providers;

use App\Services\FaultService;
use Illuminate\Support\ServiceProvider;

class FaultDataServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // Registra los datos de falla como un singleton para que la llamada se haga SOLO UNA VEZ
        $this->app->singleton('fault_data', function ($app) {
            return [
                'equipment'         => FaultService::equipment(),
                'serviceArea'       => FaultService::serviceArea(),
                'faultStatus'       => FaultService::faultStatus(),
                'sparePartStatuses' => FaultService::sparePartStatuses(),
                'projects' => FaultService::projects(),
            ];
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}

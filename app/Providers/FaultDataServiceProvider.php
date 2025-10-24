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
                'equipment'         => FaultService::equipment()->prepend('Todos', '0'),
                'serviceArea'       => FaultService::serviceArea()->prepend('Todos', '0'),
                'faultStatus'       => FaultService::faultStatus()->prepend('Todos', '0'),
                'sparePartStatuses' => FaultService::sparePartStatuses()->prepend('Todos', '0'),
                'projects' => FaultService::projects()->prepend('Todos', '0'),
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

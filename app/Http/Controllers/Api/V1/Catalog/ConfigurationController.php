<?php

namespace App\Http\Controllers\Api\V1\Catalog;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Catalog\ConfigurationRequest;
use App\Models\Configuration;
use App\Traits\Api\ApiResponse;

class ConfigurationController extends Controller
{
    use ApiResponse;

    public function __construct()
    {
        $this->middleware('permission:Configuracion Editar')->only(['update']);
    }

    public function show()
    {
        return $this->success(Configuration::first());
    }

    public function update(ConfigurationRequest $request)
    {
        $configuration = Configuration::first();

        if (!$configuration) {
            return $this->error('No existe un registro de configuración.', 404);
        }

        $configuration->tax = $request->input('tax');
        $configuration->discount = $request->input('discount');
        $configuration->save();

        return $this->success($configuration, 'Configuración actualizada exitosamente.');
    }
}

<?php

namespace App\Http\Controllers\AdminBranch;

use App\Http\Controllers\Controller;
use App\Http\Requests\ConfigurationRequest;
use App\Models\Configuration;
use App\Traits\AlertResponser;
use Illuminate\Http\Request;

class ConfigurationController extends Controller
{
    use AlertResponser;

    const INDEX = "admin.sucursal.configuration.edit";

    public function edit()
    {
        $configuration = Configuration::first();
        return view('AdminBranch.Configuration.edit', compact('configuration'));
    }

    public function update(ConfigurationRequest $request)
    {
        try {

            $conf = Configuration::first();

            $conf->tax = $request->input('tax');
            $conf->discount = $request->input('discount');
            $conf->save();

            return $this->alertSuccess(self::INDEX, 'Configuración actualizada');
        } catch (\Throwable $th) {
            return $this->alertError(self::INDEX);
        }
    }
}

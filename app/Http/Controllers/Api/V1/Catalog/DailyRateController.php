<?php

namespace App\Http\Controllers\Api\V1\Catalog;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Catalog\DailyRateRequest;
use App\Models\DailyRate;
use App\Traits\Api\ApiResponse;

/**
 * Spec §6.3.12: histórico append-only, sin update ni delete. El original
 * devolvía DailyRate::first() (el más antiguo); corregido a la tasa
 * vigente (->latest()->first()) por recomendación explícita de la spec.
 */
class DailyRateController extends Controller
{
    use ApiResponse;

    public function __construct()
    {
        $this->middleware('permission:Tasa Diaria Crear')->only(['store']);
        $this->middleware('permission:Tasa Diaria Ver')->only(['show']);
    }

    public function show()
    {
        return $this->success(DailyRate::query()->latest('id')->first());
    }

    public function store(DailyRateRequest $request)
    {
        $dailyRate = new DailyRate();
        $dailyRate->rate = $request->input('rate');
        $dailyRate->average_rate = $request->input('average_rate');
        $dailyRate->save();

        return $this->created($dailyRate, 'Tasa registrada exitosamente.');
    }
}

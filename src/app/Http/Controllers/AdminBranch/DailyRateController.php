<?php

namespace App\Http\Controllers\AdminBranch;

use App\Http\Controllers\Controller;
use App\Http\Requests\DailyRateRequest;
use App\Models\DailyRate;
use App\Traits\AlertResponser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class DailyRateController extends Controller
{
    use AlertResponser;

    const INDEX = "admin.sucursal.daily.rate.create";

    public function create()
    {
        $dailyRate = DailyRate::first();
        return view('AdminBranch.DailyRate.create', compact('dailyRate'));
    }

    public function store(DailyRateRequest $request)
    {
        try {
            $brand = new DailyRate();
            $brand->rate = $request->input('rate');
            $brand->average_rate = $request->input('average_rate');
            $brand->save();

            Session::put('dailyRate', $request->input('rate'));
            Session::put('averageRate', $request->input('average_rate'));

            return $this->alertSuccess(self::INDEX, 'Tasa actualizada');
        } catch (\Throwable $th) {
            return $this->alertError(self::INDEX);
        }
    }
}

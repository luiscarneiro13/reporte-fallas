<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ModelVehicleApiRequest;
use App\Models\ModelVehicle;
use App\Traits\ApiResponser;
use Illuminate\Http\Request;

class ModelVehicleController extends Controller
{
    use ApiResponser;

    public function store(ModelVehicleApiRequest $request)
    {
        try {
            $item = new ModelVehicle();
            $item->name = $request->input('name');
            $item->branch_id = $request->input('branch_id');
            $item->save();

            return response()->json(['success' => true, 'data' => $item]);
        } catch (\Throwable $th) {
            return response()->json(['success' => false, 'data' => null]);
        }
    }
}

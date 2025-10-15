<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\BrandApiRequest;
use App\Models\Brand;
use App\Traits\ApiResponser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BrandController extends Controller
{
    use ApiResponser;

    public function store(BrandApiRequest $request)
    {
        try {
            $brand = new Brand();
            $brand->name = $request->input('name');
            $brand->branch_id = $request->input('branch_id');
            $brand->save();

            return response()->json(['success' => true, 'data' => $brand]);
        } catch (\Throwable $th) {
            return response()->json(['success' => false, 'data' => null]);
        }
    }
}

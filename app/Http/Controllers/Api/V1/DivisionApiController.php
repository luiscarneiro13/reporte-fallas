<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\V1\Api\DivisionApiRequest;
use App\Models\Division;
use App\Traits\ApiResponser;
use Illuminate\Http\Request;

class DivisionApiController extends Controller
{
    use ApiResponser;

    public function store(DivisionApiRequest $request)
    {
        try {

            $item = new Division();
            $item->name = $request->input('name');
            $item->description = $request->input('description');
            $item->branch_id = $request->input('branch_id');
            $item->save();

            return response()->json(['success' => true, 'data' => $item]);
        } catch (\Throwable $th) {
            return response()->json(['success' => false, 'data' => null]);
        }
    }
}

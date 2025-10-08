<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\V1\Api\CustomerApiRequest;
use App\Models\Customer;
use App\Traits\ApiResponser;
use Illuminate\Http\Request;

class CustomerApiController extends Controller
{
    use ApiResponser;

    public function store(CustomerApiRequest $request)
    {
        try {

            $item = new Customer();
            $item->name = $request->input('name');
            $item->address = $request->input('address');
            $item->email = $request->input('email');
            $item->phone = $request->input('phone');
            $item->branch_id = $request->input('branch_id');
            $item->save();

            return response()->json(['success' => true, 'data' => $item]);
        } catch (\Throwable $th) {
            return response()->json(['success' => false, 'data' => null]);
        }
    }
}

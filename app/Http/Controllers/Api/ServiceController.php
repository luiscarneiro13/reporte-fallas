<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Traits\ApiResponser;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    use ApiResponser;

    public $products;

    public function __construct()
    {
        // $this->products = new ServiceRe();
    }

    public function index(Request $request)
    {
        // $branch_id = $request->input('branch_id');
        // $query = $request->input('query');
        // return response()->json(['success' => true, 'data' => $this->products->getAllBranch($branch_id, $query)]);
    }
}

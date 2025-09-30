<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Repositories\ProductRepository;
use App\Http\Requests\ProductApiRequest;
use App\Models\Product;
use App\Traits\ApiResponser;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    use ApiResponser;

    public $products;

    public function __construct()
    {
        $this->products = new ProductRepository();
    }

    public function index(Request $request)
    {
        $branch_id = $request->input('branch_id');
        $query = $request->input('query');
        return response()->json(['success' => true, 'data' => $this->products->getAllBranch($branch_id, $query)]);
    }
}

<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Sale;
use Illuminate\Http\Request;

class SalesHistoryController extends Controller
{
    public function getSaleId($id)
    {
        return response()->json(['data' => Sale::where('id', $id)->first()]);
    }
}

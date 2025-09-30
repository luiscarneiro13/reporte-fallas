<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use Illuminate\Http\Request;

class PruebaController extends Controller
{
    public function index($id)
    {
        return Sale::find($id);
    }
}

<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Administrador de sucursal
use App\Http\Controllers\Api\BrandController as ApiBrand;
use App\Http\Controllers\Api\ModelVehicleController as ApiModelVehicle;
use App\Http\Controllers\Api\TypeArticleController as ApiTypeArticle;
use App\Http\Controllers\Api\CustomerController as ApiCustomer;
use App\Http\Controllers\Api\ProductController as ApiProduct;
use App\Http\Controllers\Api\ServiceController as ApiService;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::prefix('admin-sucursal')->group(function () {
    Route::post('marcas/store', [ApiBrand::class, 'store']);
    Route::post('modelovehiculos/store', [ApiModelVehicle::class, 'store']);
    Route::post('tipos-articulos/store', [ApiTypeArticle::class, 'store']);
    Route::get('cliente/index', [ApiCustomer::class, 'index']);
    Route::post('cliente/store', [ApiCustomer::class, 'store']);
    Route::get('productos/index', [ApiProduct::class, 'index']);
    Route::get('servicios/index', [ApiService::class, 'index']);
});

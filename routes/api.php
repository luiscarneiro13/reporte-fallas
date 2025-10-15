<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Administrador de sucursal
use App\Http\Controllers\Api\BrandController as ApiBrand;
use App\Http\Controllers\Api\ModelVehicleController as ApiModelVehicle;
use App\Http\Controllers\Api\TypeArticleController as ApiTypeArticle;
use App\Http\Controllers\Api\CustomerController as ApiCustomer;
use App\Http\Controllers\Api\ServiceController as ApiService;
use App\Http\Controllers\Api\V1\CustomerApiController;
use App\Http\Controllers\Api\V1\DivisionApiController;

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
    Route::get('servicios/index', [ApiService::class, 'index']);
});

Route::prefix('v1/admin')->group(function () {
    Route::post('clientes/store', [CustomerApiController::class, 'store']);
    Route::post('divisiones/store', [DivisionApiController::class, 'store']);
});

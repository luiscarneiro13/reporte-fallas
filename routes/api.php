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

/*
|--------------------------------------------------------------------------
| API REST /api/v1 (docs/api-endpoints-spec.md) — Fase 1: núcleo
|--------------------------------------------------------------------------
|
| Auth (Sanctum) + CRUD sucursal-scoped + catálogos legado. No incluye
| sync offline-first, push notifications ni el namespace super-admin
| (ver checklist de estado al final de docs/api-endpoints-spec.md).
|
*/

use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\AdminBranch\CustomerController as V1CustomerController;
use App\Http\Controllers\Api\V1\AdminBranch\DivisionController as V1DivisionController;
use App\Http\Controllers\Api\V1\AdminBranch\EmployeeController as V1EmployeeController;
use App\Http\Controllers\Api\V1\AdminBranch\EquipmentController as V1EquipmentController;
use App\Http\Controllers\Api\V1\AdminBranch\EquipmentTypeController as V1EquipmentTypeController;
use App\Http\Controllers\Api\V1\AdminBranch\ExecutorController as V1ExecutorController;
use App\Http\Controllers\Api\V1\AdminBranch\FaultController as V1FaultController;
use App\Http\Controllers\Api\V1\AdminBranch\FaultStatusController as V1FaultStatusController;
use App\Http\Controllers\Api\V1\AdminBranch\OwnerController as V1OwnerController;
use App\Http\Controllers\Api\V1\AdminBranch\ProjectController as V1ProjectController;
use App\Http\Controllers\Api\V1\AdminBranch\ServiceAreaController as V1ServiceAreaController;
use App\Http\Controllers\Api\V1\AdminBranch\SparePartStatusController as V1SparePartStatusController;
use App\Http\Controllers\Api\V1\Catalog\AdministradoresController as V1AdministradoresController;
use App\Http\Controllers\Api\V1\Catalog\BrandController as V1BrandController;
use App\Http\Controllers\Api\V1\Catalog\ConfigurationController as V1ConfigurationController;
use App\Http\Controllers\Api\V1\Catalog\DailyRateController as V1DailyRateController;
use App\Http\Controllers\Api\V1\Catalog\MethodPaymentController as V1MethodPaymentController;
use App\Http\Controllers\Api\V1\Catalog\ModelVehicleController as V1ModelVehicleController;
use App\Http\Controllers\Api\V1\Catalog\MyBranchController as V1MyBranchController;
use App\Http\Controllers\Api\V1\Catalog\OperatorController as V1OperatorController;
use App\Http\Controllers\Api\V1\Catalog\ServiceController as V1ServiceController;
use App\Http\Controllers\Api\V1\Catalog\SupervisorController as V1SupervisorController;
use App\Http\Controllers\Api\V1\Catalog\SupplierController as V1SupplierController;
use App\Http\Controllers\Api\V1\Catalog\TypeArticleController as V1TypeArticleController;

Route::prefix('v1')->middleware('sanctum.guard')->group(function () {
    Route::get('health', function () {
        return response()->json(['success' => true, 'message' => 'OK', 'data' => ['status' => 'ok']]);
    });

    // TODO: min_supported_version/latest_version son placeholders — ajustar al
    // esquema de versionado real de la app móvil cuando se defina.
    Route::get('app/version', function () {
        return response()->json(['success' => true, 'message' => 'OK', 'data' => [
            'min_supported_version' => '1.0.0',
            'latest_version' => '1.0.0',
            'force_update' => false,
        ]]);
    });

    Route::post('login', [AuthController::class, 'login'])->middleware('throttle:login-api');

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('logout', [AuthController::class, 'logout']);
        Route::post('logout-all', [AuthController::class, 'logoutAll']);

        // NOTA: el middleware check.subscription de la spec §2 depende de un
        // modelo Subscription/Plan que este proyecto no tiene (se decidió
        // omitirlo en la Fase 1 — ver checklist en docs/api-endpoints-spec.md).
        Route::group([], function () {
            Route::apiResource('clientes', V1CustomerController::class)->parameters(['clientes' => 'id']);
            Route::apiResource('divisiones', V1DivisionController::class)->parameters(['divisiones' => 'id']);

            Route::apiResource('empleados', V1EmployeeController::class)->parameters(['empleados' => 'id']);
            Route::apiResource('ejecutores', V1ExecutorController::class)->parameters(['ejecutores' => 'id']);

            Route::get('equipos/{id}/historial', [V1EquipmentController::class, 'historial']);
            Route::apiResource('equipos', V1EquipmentController::class)->parameters(['equipos' => 'id']);
            Route::apiResource('tipos-equipo', V1EquipmentTypeController::class)->parameters(['tipos-equipo' => 'id']);

            Route::get('fallas/crear-datos', [V1FaultController::class, 'createData']);
            Route::apiResource('fallas', V1FaultController::class)->parameters(['fallas' => 'id']);

            Route::apiResource('estados-falla', V1FaultStatusController::class)->parameters(['estados-falla' => 'id']);
            Route::apiResource('propietarios', V1OwnerController::class)->parameters(['propietarios' => 'id']);
            Route::apiResource('proyectos', V1ProjectController::class)->parameters(['proyectos' => 'id']);
            Route::apiResource('areas-servicio', V1ServiceAreaController::class)->parameters(['areas-servicio' => 'id']);
            Route::apiResource('estados-repuestos', V1SparePartStatusController::class)->parameters(['estados-repuestos' => 'id']);

            // Catálogos legado (namespace AdminBranch en la spec §6.3)
            Route::apiResource('marcas', V1BrandController::class)->parameters(['marcas' => 'id']);
            Route::apiResource('modelos-vehiculos', V1ModelVehicleController::class)->parameters(['modelos-vehiculos' => 'id']);
            Route::apiResource('tipos-articulos', V1TypeArticleController::class)->parameters(['tipos-articulos' => 'id']);
            Route::apiResource('proveedores', V1SupplierController::class)->parameters(['proveedores' => 'id']);
            Route::apiResource('servicios', V1ServiceController::class)->parameters(['servicios' => 'id']);
            Route::apiResource('metodos-pago', V1MethodPaymentController::class)->parameters(['metodos-pago' => 'id']);

            Route::get('operadores/{id}/edit', [V1OperatorController::class, 'edit']);
            Route::apiResource('operadores', V1OperatorController::class)->only(['index', 'store', 'update', 'destroy'])->parameters(['operadores' => 'id']);

            Route::get('supervisores/{id}/edit', [V1SupervisorController::class, 'edit']);
            Route::apiResource('supervisores', V1SupervisorController::class)->only(['index', 'store', 'update', 'destroy'])->parameters(['supervisores' => 'id']);

            Route::get('administradores/{id}/edit', [V1AdministradoresController::class, 'edit']);
            Route::apiResource('administradores', V1AdministradoresController::class)->only(['index', 'store', 'update', 'destroy'])->parameters(['administradores' => 'id']);

            Route::get('mi-sucursal', [V1MyBranchController::class, 'show']);
            Route::put('mi-sucursal/{id}', [V1MyBranchController::class, 'update']);

            Route::get('configuracion', [V1ConfigurationController::class, 'show']);
            Route::put('configuracion', [V1ConfigurationController::class, 'update']);

            Route::get('tasa-diaria', [V1DailyRateController::class, 'show']);
            Route::post('tasa-diaria', [V1DailyRateController::class, 'store']);
        });
    });
});

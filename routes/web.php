<?php

use App\Http\Controllers\SuperAdmin\AssignRoleController;
use App\Http\Controllers\SuperAdmin\PermissionController;
use App\Http\Controllers\SuperAdmin\RoleController;
use App\Http\Controllers\SuperAdmin\UserController;
use App\Http\Controllers\SuperAdmin\BranchController;

use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;

use Illuminate\Support\Facades\Artisan;


// Administrador de sucursal
// *************************************************************************************
use App\Http\Controllers\AdminBranch\MyBranchController as AdminBranchMyBranch;
use App\Http\Controllers\AdminBranch\BrandController as AdminBranchBrand;
use App\Http\Controllers\AdminBranch\ModelVehicleController as AdminBranchBrandVehicle;
use App\Http\Controllers\AdminBranch\TypeArticleController as AdminBranchTypeArticle;
use App\Http\Controllers\AdminBranch\SupplierController as AdminBranchSupplier;
use App\Http\Controllers\AdminBranch\ConfigurationController as AdminBranchConfiguration;
use App\Http\Controllers\AdminBranch\DailyRateController as AdminBranchDailyRate;
use App\Http\Controllers\AdminBranch\ServiceController as AdminBranchService;
use App\Http\Controllers\AdminBranch\AdministradoresController as AdminBranchAdministradores;
use App\Http\Controllers\AdminBranch\MethodPaymentController as AdminBranchMethodPayment;
use App\Http\Controllers\AdminBranch\OperatorController;
use App\Http\Controllers\AdminBranch\SupervisorController;
use App\Http\Controllers\PruebaController;
use App\Http\Controllers\V1\AdminBranch\CustomerController;
use App\Http\Controllers\V1\AdminBranch\DivisionController;
use App\Http\Controllers\V1\AdminBranch\EmployeeController;
use App\Http\Controllers\V1\AdminBranch\EquipmentController;
use App\Http\Controllers\V1\AdminBranch\ExecutorController;
use App\Http\Controllers\V1\AdminBranch\FaultController;
use App\Http\Controllers\V1\AdminBranch\FaultHistoryController;
use App\Http\Controllers\V1\AdminBranch\FaultStatusController;
use App\Http\Controllers\V1\AdminBranch\OwnerController;
use App\Http\Controllers\V1\AdminBranch\ProjectController;
use App\Http\Controllers\V1\AdminBranch\ServiceAreaController;
use App\Http\Controllers\V1\AdminBranch\SparePartStatusController;

// *************************************************************************************

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/clear-cache', function () {
    Artisan::call('cache:clear');
    Artisan::call('config:cache');
    Artisan::call('config:clear');
    Artisan::call('route:cache');
    Artisan::call('route:clear');
    Artisan::call('view:clear');
    return 'Application cache cleared';
});


Route::get('/prueba/{id}', [PruebaController::class, 'index']);

Route::get('/', [LoginController::class, 'create'])->name('login.create');
Route::post('/', [LoginController::class, 'store'])->name('login.store');

Route::get('/logout', function () {
    return redirect(url('/'));
});

Route::get('/login', function () {
    return redirect(url('/'));
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::resource('/roles', RoleController::class)->names('roles');
    Route::resource('/permissions', PermissionController::class)->names('permissions');
    Route::resource('/assign_role', AssignRoleController::class)->names('assign_role');
    Route::resource('/users', UserController::class)->names('users');
    Route::resource('/branches', BranchController::class)->names('branches');

    Route::prefix('admin-general')->group(function () {
        // Route::resource('/brands', AdminBrand::class)->names('admin.brands');
    });

    Route::prefix('admin-sucursal')->group(function () {
        Route::get('/configuracion', [AdminBranchConfiguration::class, 'edit'])->name('admin.sucursal.configuration.edit');
        Route::post('/configuracion', [AdminBranchConfiguration::class, 'update'])->name('admin.sucursal.configuration.update');
        Route::get('/daily-rate', [AdminBranchDailyRate::class, 'create'])->name('admin.sucursal.daily.rate.create');
        Route::post('/daily-rate', [AdminBranchDailyRate::class, 'store'])->name('admin.sucursal.daily.rate.store');


        Route::resource('/marcas', AdminBranchBrand::class)->names('admin.sucursal.brands');
        Route::resource('/modelosvehiculos', AdminBranchBrandVehicle::class)->names('admin.sucursal.models.vehicles');
        Route::resource('/tipos-articulos', AdminBranchTypeArticle::class)->names('admin.sucursal.type.articles');
        Route::resource('/proveedores', AdminBranchSupplier::class)->names('admin.sucursal.suppliers');
        Route::resource('/servicios', AdminBranchService::class)->names('admin.sucursal.services');
        Route::resource('/metodos-pago', AdminBranchMethodPayment::class)->names('admin.sucursal.method.payment');
    });

    Route::prefix('v1/admin')->group(function () {

        Route::get('/mi-sucursal/edit', [AdminBranchMyBranch::class, 'edit'])->name('admin.branch.my-branch');
        Route::put('/mi-sucursal/update/{id}', [AdminBranchMyBranch::class, 'update'])->name('admin.branch.my-branch.update');
        Route::resource('/operadores', OperatorController::class)->names('admin.sucursal.usuarios.operators');
        Route::resource('/supervisores', SupervisorController::class)->names('admin.sucursal.usuarios.supervisors');
        Route::resource('/administradores', AdminBranchAdministradores::class)->names('admin.sucursal.usuarios.administradores');

        Route::resource('/propietarios', OwnerController::class)->names('admin.sucursal.owners');
        Route::resource('/areas-de-servicio', ServiceAreaController::class)->names('admin.sucursal.service.areas');
        Route::resource('/divisiones', DivisionController::class)->names('admin.sucursal.divisions');
        Route::resource('/clientes', CustomerController::class)->names('admin.sucursal.customers');
        Route::resource('/proyectos', ProjectController::class)->names('admin.sucursal.projects');
        Route::resource('/equipos', EquipmentController::class)->names('admin.sucursal.equipment');
        Route::resource('/status-fallas', FaultStatusController::class)->names('admin.sucursal.fault.statuses');
        Route::resource('/status-repuestos', SparePartStatusController::class)->names('admin.sucursal.spare.part.statuses');
        Route::resource('/empleados', EmployeeController::class)->names('admin.sucursal.employees');
        Route::resource('/ejecutores', ExecutorController::class)->names('admin.sucursal.executors');
        Route::resource('/fallas', FaultController::class)->names('admin.sucursal.faults');
        Route::resource('/historico-fallas', FaultHistoryController::class)->names('admin.sucursal.fault.history');
    });
});

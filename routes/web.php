<?php

use App\Http\Controllers\SuperAdmin\AssignRoleController;
use App\Http\Controllers\SuperAdmin\PermissionController;
use App\Http\Controllers\SuperAdmin\RoleController;
use App\Http\Controllers\SuperAdmin\UserController;
use App\Http\Controllers\SuperAdmin\BranchController;

use App\Http\Controllers\Admin\CustomerController;
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
use App\Http\Controllers\AdminBranch\ProductsController as AdminBranchProduct;
use App\Http\Controllers\AdminBranch\SupplierController as AdminBranchSupplier;
use App\Http\Controllers\AdminBranch\ProductEntryController as AdminBranchProductEntry;
use App\Http\Controllers\AdminBranch\ConfigurationController as AdminBranchConfiguration;
use App\Http\Controllers\AdminBranch\DailyRateController as AdminBranchDailyRate;
use App\Http\Controllers\AdminBranch\ServiceController as AdminBranchService;
use App\Http\Controllers\AdminBranch\EmployeeController as AdminBranchEmployee;
use App\Http\Controllers\AdminBranch\CajerosController as AdminBranchCajeros;
use App\Http\Controllers\AdminBranch\AdministradoresController as AdminBranchAdministradores;
use App\Http\Controllers\AdminBranch\CustomerController as AdminBranchCustomer;
use App\Http\Controllers\AdminBranch\MethodPaymentController as AdminBranchMethodPayment;
use App\Http\Controllers\AdminBranch\SalesHistoryController as AdminBranchSalesHistory;
use App\Http\Controllers\AdminBranch\SalesDailyController as AdminBranchSalesDaily;
use App\Http\Controllers\Api\SalesHistoryController as ApiSale;
use App\Http\Controllers\PruebaController;
// *************************************************************************************

// Vender
// *************************************************************************************
use App\Http\Controllers\SellController as Sell;
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
Route::get('/clear-cache', function() {
    Artisan::call('cache:clear');
    Artisan::call('config:cache');
    Artisan::call('config:clear');
    Artisan::call('route:cache');
    Artisan::call('route:clear');
    Artisan::call('view:clear');
    return 'Application cache cleared';
});




Route::get('/api/sale/{id}', [ApiSale::class, 'getSaleId']);

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

    Route::resource('/customer', CustomerController::class)->names('customer');
    Route::resource('/roles', RoleController::class)->names('roles');
    Route::resource('/permissions', PermissionController::class)->names('permissions');
    Route::resource('/assign_role', AssignRoleController::class)->names('assign_role');
    Route::resource('/users', UserController::class)->names('users');
    Route::resource('/branchs', BranchController::class)->names('branchs');

    Route::prefix('admin-general')->group(function () {
        // Route::resource('/brands', AdminBrand::class)->names('admin.brands');
    });

    Route::prefix('admin-sucursal')->group(function () {
        Route::get('/configuracion', [AdminBranchConfiguration::class, 'edit'])->name('admin.sucursal.configuration.edit');
        Route::post('/configuracion', [AdminBranchConfiguration::class, 'update'])->name('admin.sucursal.configuration.update');
        Route::get('/daily-rate', [AdminBranchDailyRate::class, 'create'])->name('admin.sucursal.daily.rate.create');
        Route::post('/daily-rate', [AdminBranchDailyRate::class, 'store'])->name('admin.sucursal.daily.rate.store');
        Route::get('/mi-sucursal/edit', [AdminBranchMyBranch::class, 'edit'])->name('admin.branch.my-branch');
        Route::put('/mi-sucursal/update/{id}', [AdminBranchMyBranch::class, 'update'])->name('admin.branch.my-branch.update');

        Route::resource('/marcas', AdminBranchBrand::class)->names('admin.sucursal.brands');
        Route::resource('/modelosvehiculos', AdminBranchBrandVehicle::class)->names('admin.sucursal.models.vehicles');
        Route::resource('/tipos-articulos', AdminBranchTypeArticle::class)->names('admin.sucursal.type.articles');
        Route::resource('/productos', AdminBranchProduct::class)->names('admin.sucursal.products');
        Route::resource('/proveedores', AdminBranchSupplier::class)->names('admin.sucursal.suppliers');
        Route::resource('/entrada-productos', AdminBranchProductEntry::class)->names('admin.sucursal.product.entries');
        Route::resource('/servicios', AdminBranchService::class)->names('admin.sucursal.services');
        Route::resource('/vendedores', AdminBranchEmployee::class)->names('admin.sucursal.usuarios.employees');
        Route::resource('/cajeros', AdminBranchCajeros::class)->names('admin.sucursal.usuarios.cajeros');
        Route::resource('/administradores', AdminBranchAdministradores::class)->names('admin.sucursal.usuarios.administradores');
        Route::resource('/clientes', AdminBranchCustomer::class)->names('admin.sucursal.customers');
        Route::resource('/metodos-pago', AdminBranchMethodPayment::class)->names('admin.sucursal.method.payment');

        Route::resource('/historial-ventas', AdminBranchSalesHistory::class)->names('admin.sucursal.sales.history');
        Route::resource('/ventas-del-dia', AdminBranchSalesDaily::class)->names('admin.sucursal.sales.daily');
    });

    Route::get('/vender', [Sell::class, 'index'])->name('sell.index');
    Route::post('/api/vender', [Sell::class, 'store'])->name('sell.store');
    Route::post('/api/finalizar/{sale_id}', [AdminBranchSalesHistory::class, 'update']);
});

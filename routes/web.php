<?php

use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\SaleController;
use App\Http\Controllers\Admin\BusinessSettingController;
use App\Http\Controllers\Admin\TaskController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Pos\PosApiController;
use App\Http\Controllers\Pos\PosController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'create'])->name('login');
    Route::post('/login', [LoginController::class, 'store']);
});

Route::post('/logout', [LoginController::class, 'destroy'])->name('logout')->middleware('auth');

Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', fn () => view('admin.dashboard'))->name('dashboard');

    // Inventory
    Route::get('/inventario', [ProductController::class, 'index'])->name('inventory.index');
    Route::post('/inventario/productos', [ProductController::class, 'store'])->name('products.store');
    Route::put('/inventario/productos/{product}', [ProductController::class, 'update'])->name('products.update');
    Route::delete('/inventario/productos/{product}', [ProductController::class, 'destroy'])->name('products.destroy');
    Route::post('/inventario/productos/{product}/ajuste', [ProductController::class, 'adjustStock'])->name('products.adjust-stock');
    Route::post('/inventario/categorias', [CategoryController::class, 'store'])->name('categories.store');
    Route::put('/inventario/categorias/{category}', [CategoryController::class, 'update'])->name('categories.update');
    Route::delete('/inventario/categorias/{category}', [CategoryController::class, 'destroy'])->name('categories.destroy');

    // Sales
    Route::get('/ventas', [SaleController::class, 'index'])->name('sales.index');
    Route::get('/ventas/{sale}', [SaleController::class, 'show'])->name('sales.show');

    // Reports
    Route::get('/reportes', [ReportController::class, 'index'])->name('reports.index');

    // Business Settings
    Route::get('/negocio', [BusinessSettingController::class, 'edit'])->name('business.edit');
    Route::put('/negocio', [BusinessSettingController::class, 'update'])->name('business.update');

    // Users
    Route::get('/usuarios', [UserController::class, 'index'])->name('users.index');
    Route::post('/usuarios', [UserController::class, 'store'])->name('users.store');
    Route::put('/usuarios/{user}', [UserController::class, 'update'])->name('users.update');
    Route::patch('/usuarios/{user}/toggle-active', [UserController::class, 'toggleActive'])->name('users.toggle-active');

    // Tasks
    Route::get('/tareas', [TaskController::class, 'index'])->name('tasks.index');
    Route::post('/tareas', [TaskController::class, 'store'])->name('tasks.store');
    Route::put('/tareas/{task}', [TaskController::class, 'update'])->name('tasks.update');
    Route::patch('/tareas/{task}/toggle', [TaskController::class, 'toggle'])->name('tasks.toggle');
    Route::delete('/tareas/{task}', [TaskController::class, 'destroy'])->name('tasks.destroy');
});

Route::middleware(['auth', 'role:seller'])->prefix('pos')->name('pos.')->group(function () {
    Route::get('/', fn () => redirect()->route('pos.sale'))->name('index');
    Route::get('/venta', [PosController::class, 'sale'])->name('sale');
    Route::get('/buscar', [PosController::class, 'search'])->name('search');
    Route::get('/estado', [PosController::class, 'status'])->name('status');

    // API endpoints (implemented in later phases)
    Route::get('/api/products', [PosApiController::class, 'searchProducts'])->name('api.products');
    Route::post('/api/sales', [PosApiController::class, 'storeSale'])->name('api.sales.store');
});

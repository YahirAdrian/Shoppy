<?php

use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\SaleController;
use App\Http\Controllers\Admin\BusinessSettingController;
use App\Http\Controllers\Auth\LoginController;
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
});

Route::middleware(['auth', 'role:seller'])->prefix('pos')->name('pos.')->group(function () {
    Route::get('/', fn () => view('pos.index'))->name('index');
});

<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BrandController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\LocationProductController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProductUnitController;
use App\Http\Controllers\StockMovementController;
use App\Http\Controllers\SupplierController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


//Admin panels
Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('products', ProductController::class);
    Route::get('products/{id}/stock', [ProductController::class, 'getStock']);

    Route::apiResource('brands', BrandController::class);
    Route::apiResource('categories', CategoryController::class);
    Route::apiResource('product-units', ProductUnitController::class)->except(['show']);
    Route::apiResource('suppliers', SupplierController::class);
    Route::apiResource('locations', LocationController::class)->except(['show']);

    // Stock Management
    Route::post('stock/incoming', [StockMovementController::class, 'incoming']); // Add stock - IN
    Route::post('stock/transfer', [StockMovementController::class, 'transfer']); // Transfer between locations -TRANSFER
    Route::post('stock/adjustment', [StockMovementController::class, 'adjustment']); // Manual adjustment - ADJUSTMENT
    Route::get('stock/movements', [StockMovementController::class, 'movements']); // History OUT

    //Stocks Management
    Route::apiResource('location-products', LocationProductController::class);
});


//Admin Only 
Route::post('login', [AuthController::class, 'login']);
Route::post('register', [AuthController::class, 'register']);
Route::post('logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');

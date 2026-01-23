<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BrandController;
use App\Http\Controllers\CartItemController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ChequeController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\LocationProductController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProductUnitController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\StockMovementController;
use App\Http\Controllers\SupplierController;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Auth\EmailVerificationRequest;

// Send verification email
Route::middleware('auth:sanctum')->get('/email/verify', function (Request $request) {
    return response()->json([
        'message' => 'Email not verified',
        'verification_url' => $request->user()->verificationUrl()
    ]);
})->name('verification.notice');

// Verify email
Route::get('/email/verify/{id}/{hash}', function (Request $request) {
    $user = User::find($request->id);
    $user->forceFill([
        'email_verified_at' => now(),
    ])->save();
    return response()->json(['message' => 'Email verified successfully']);
})->middleware(['signed'])->name('verification.verify');

// Resend verification email
Route::middleware(['auth:sanctum'])->post('/email/resend', function (Request $request) {
    if ($request->user()->hasVerifiedEmail()) {
        return response()->json(['message' => 'Email already verified']);
    }
    $request->user()->sendEmailVerificationNotification();
    return response()->json(['message' => 'Verification link sent']);
})->name('verification.resend');

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


//Admin panels
//Route::middleware('auth:sanctum')->group(function () {
Route::apiResource('products', ProductController::class);
Route::post('products/{product}/units', [ProductController::class, 'storeUnit']);
Route::patch('products/{product}/units/{unit}', [ProductController::class, 'updateUnits']);
Route::delete('products/{product}/units/{unit}', [ProductController::class, 'destroyUnit']);
Route::get('products/{product}/stock', [ProductController::class, 'getStock']);
// Route::apiResource('sales', SaleController::class);
// Route::delete('sales/cancel/{sale}', [SaleController::class, 'cancel']);
Route::apiResource('brands', BrandController::class);
Route::apiResource('categories', CategoryController::class);
Route::apiResource('product-units', ProductUnitController::class);
Route::apiResource('suppliers', SupplierController::class);
Route::apiResource('locations', LocationController::class);

// Stock Movements
Route::post('stock/incoming', [StockMovementController::class, 'incoming']); // Add stock - IN
Route::post('stock/transfer', [StockMovementController::class, 'transfer']); // Transfer between locations -TRANSFER
Route::post('stock/adjustment', [StockMovementController::class, 'adjustment']); // Manual adjustment - ADJUSTMENT
Route::get('stock/movements', [StockMovementController::class, 'movements']); // History OUT
Route::get('stock/movements/{type}', [StockMovementController::class, 'movement']); // History OUT

//Stocks Management
Route::apiResource('location-products', LocationProductController::class);
Route::get('stock-level', [LocationProductController::class, 'stockLevel']);
Route::get('stock-levelbyLocation/{location}', [LocationProductController::class, 'stockLevelByLocation']);
Route::get('stock-levelbyProduct/{id}', [LocationProductController::class, 'stockLevelByProduct']);
Route::get('stock-levelbyCategory/{id}', [LocationProductController::class, 'stockLevelByCategory']);
Route::get('stock-levelbyBrand/{id}', [LocationProductController::class, 'stockLevelByBrand']);


//Cart
Route::apiResource('cart', CartItemController::class)->middleware('auth:sanctum');
Route::get('cart', [CartItemController::class, 'index'])->middleware('auth:sanctum');
Route::delete('cart/{cartItem}', [CartItemController::class, 'destroy'])->middleware('auth:sanctum');
//Cheques
Route::get('cheques', [ChequeController::class, 'index']);
Route::post('cheques', [ChequeController::class, 'store']);
Route::patch('cheques/{cheque}', [ChequeController::class, 'update']);
Route::get('cheques/pending', [ChequeController::class, 'pendingCheques']);
//});

//Route::middleware('auth:sanctum', 'role:admin', 'verified.api')->group(function () {
Route::apiResource('sales', SaleController::class);
Route::delete('sales/cancel/{sale}', [SaleController::class, 'cancel']);
//});


//Admin Only 
Route::post('login', [AuthController::class, 'login']);
Route::post('register', [AuthController::class, 'register']);
Route::post('logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');

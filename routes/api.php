<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BrandController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ChequeController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\LocationProductController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProductUnitController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\StockMovementController;
use App\Http\Controllers\SupplierController;
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
Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
    $request->fulfill();
    return response()->json(['message' => 'Email verified successfully']);
})->middleware(['auth:sanctum', 'signed'])->name('verification.verify');

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
Route::get('stock-level/{location}', [LocationProductController::class, 'stockLevelByLocation']);


//Cheques
Route::get('cheques', [ChequeController::class, 'index']);
Route::get('cheques/pending', [ChequeController::class, 'pendingCheques']);


//});
Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('sales', SaleController::class)->middleware('role:admin,user');
    Route::delete('sales/cancel/{sale}', [SaleController::class, 'cancel'])->middleware('role:admin,user');
});


//Admin Only 
Route::post('login', [AuthController::class, 'login']);
Route::post('register', [AuthController::class, 'register']);
Route::post('logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');



Route::get('test-mail', function () {
    Mail::raw('Mailtrap is working 🎉', function ($message) {
        $message->to('test@example.com')
            ->subject('Mailtrap Test');
    });

    return 'Mail sent';
});

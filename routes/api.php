<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BusinessController;
use App\Http\Controllers\MediaController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\ReviewController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


/** Authentication, verification and reset password routes*/
Route::post('/login', [AuthController::class, 'login']);

Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');

Route::post('/register', [AuthController::class, 'register']);

Route::get('/email/verify/{id}/{hash}', [AuthController::class, 'verify'])
    ->middleware(['signed'])// Middleware for digital signed urls (for security)
    ->name('verification.verify');

Route::post('/email/resend', [AuthController::class, 'resend'])
    ->middleware(['auth:sanctum', 'throttle:2,1'])// Middlware for limit 2 request per minute
    ->name('verification.send');

Route::post('/forgot-password', [AuthController::class, 'sendResetLinkEmail'])->name('password.email');

Route::post('/reset-password', [AuthController::class, 'reset'])->name('password.update');
/** END:Authentication, verification and reset password routes*/

/** Business routes*/
Route::middleware('auth:sanctum')->group( function () {
    Route::resource('businesses', BusinessController::class)->only([
        'index', 'store', 'show', 'update', 'destroy'
    ]);
});
Route::get('businesses/{business}', [BusinessController::class, 'show']);
Route::get('businesses/{business}/products', [BusinessController::class, 'getProducts']);
Route::get('businesses/{business}/services', [BusinessController::class, 'getServices']);
/**END: Business routes*/

/** Media routes*/
Route::middleware('auth:sanctum')->group( function () {
    Route::resource('media', MediaController::class)->only([
        'index', 'store', 'show', 'update', 'destroy'
    ]);
});
/**END: Media routes*/

/** Product routes*/
Route::middleware('auth:sanctum')->group( function () {
    Route::resource('products', ProductController::class)->only([
        'store', 'update', 'destroy'
    ]);
});
Route::get('products/{product}', [ProductController::class, 'show']);
/**END: Product routes*/

/** Services routes*/
Route::middleware('auth:sanctum')->group( function () {
    Route::resource('services', ServiceController::class)->only([
        'store', 'update', 'destroy'
    ]);
});
Route::get('services/{service}', [ServiceController::class, 'show']);
/**END: Services routes*/

/** Review routes*/
Route::middleware('auth:sanctum')->group( function () {
    Route::resource('reviews', ReviewController::class)->only([
        'store', 'update', 'destroy'
    ]);
});
Route::get('reviews', [ReviewController::class, 'index']);
Route::get('reviews/{review}', [ReviewController::class, 'show']);
/**END: Review routes*/
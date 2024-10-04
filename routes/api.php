<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BusinessController;
use App\Http\Controllers\MediaController;
use App\Http\Controllers\ProductController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


/** Authentication routes*/
Route::post('/login', [AuthController::class, 'login']);

Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');

Route::post('/register', [AuthController::class, 'register']);
/** END:Authentication routes*/

/** Business routes*/
Route::middleware('auth:sanctum')->group( function () {
    Route::resource('businesses', BusinessController::class)->only([
        'index', 'store', 'show', 'update', 'destroy'
    ]);
    Route::get('businesses/{business}/products', [BusinessController::class, 'getProducts']);
});
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
        'store', 'show', 'update', 'destroy'
    ]);
});
/**END: Product routes*/
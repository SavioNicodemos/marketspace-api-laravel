<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\ApiAuthController;
use App\Http\Controllers\ProductController;

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

Route::group(['prefix' => 'v1'], function () {
    // Not authenticated routes
    Route::post('/sessions', [ApiAuthController::class, 'login'])->name('login.api');
    Route::post('/users', [ApiAuthController::class, 'register'])->name('register.api');

    Route::middleware('auth:api')->group(function () {
        Route::post('/logout', [ApiAuthController::class, 'logout'])->name('logout.api');

        Route::get("users/me", [ApiAuthController::class, 'me'])->name('users.me');

        Route::get('users/products', [ProductController::class, 'getMyProducts']);
        Route::apiResource('products', ProductController::class);
    });
});

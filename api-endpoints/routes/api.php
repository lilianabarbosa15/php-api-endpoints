<?php

use App\Http\Controllers\Api\v1\ShoppingCartController;
use App\Http\Controllers\Api\v1\AuthController;
use App\Http\Controllers\Api\v1\OrderController;
use App\Http\Controllers\Api\v1\ProductController;

use Illuminate\Support\Facades\Route;



Route::prefix('v1')->group(function () {

    //authentication
    Route::post('/register', [AuthController::class, 'register'])->name('register');
    Route::post('/login', [AuthController::class, 'login'])->name('login');
    Route::middleware('auth:sanctum')->get('profile', [AuthController::class, 'profile'])->name('profile');
    Route::post('logout', [AuthController::class, 'logout'])->name('logout');

    //shopping cart
    Route::prefix('cart')->middleware('auth:sanctum')->group( function () {
        Route::get('/', [ShoppingCartController::class, 'index']);
        Route::post('/add', [ShoppingCartController::class, 'store']);
        Route::put('/update/{id}', [ShoppingCartController::class, 'update']);
        Route::delete('/remove/{id}', [ShoppingCartController::class, 'destroy']);
    });

    //products
    Route::prefix('products')->group( function () {
        Route::get('/search', [ProductController::class, 'search']);
        Route::get('/', [ProductController::class, 'index']);
        Route::post('/', [ProductController::class, 'store']);          // should be private
        Route::get('/{id}', [ProductController::class, 'show']);
        Route::put('/{id}', [ProductController::class, 'update']);      // should be private
        Route::delete('/{id}', [ProductController::class, 'destroy']);  // should be private
    });

    //orders
    Route::prefix('orders')->middleware('auth:sanctum')->group( function () {
	    Route::get('/', [OrderController::class, 'index']);
        Route::get('/{id}', [OrderController::class, 'show']);
        Route::post('/create', [OrderController::class, 'store']); 
    });

});
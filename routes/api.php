<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::apiResource('products', \App\Http\Controllers\Api\ProductController::class)
    ->only(['index', 'store'])
    ->names('api.products');

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

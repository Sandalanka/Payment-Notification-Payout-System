<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PaymentUploadController;



Route::prefix('v1')->group(function () {
    Route::prefix('user')->group(function () {
        Route::post('register', [AuthController::class, 'register']);
        Route::post('login', [AuthController::class, 'login']);
        Route::get('logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
    });

    Route::prefix('payment')->group(function () {
        Route::post('upload', [PaymentUploadController::class, 'upload'])->middleware('auth:sanctum');
    });
});


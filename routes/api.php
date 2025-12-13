<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LiveController;
use App\Http\Controllers\LiveTokenController;
Route::prefix('V1')->group(function () {

    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/lives', [LiveController::class, 'index']);
        Route::post('/lives', [LiveController::class, 'store']);            // create live record
        Route::post('/lives/{live}/start', [LiveController::class, 'start']);
        Route::post('/lives/{live}/stop', [LiveController::class, 'stop']);
        Route::post('/lives/{live}/promote', [LiveController::class, 'promote']); // promote viewer -> host

        // Token endpoint (returns token based on role param)
        Route::get('/livesToken/{live}/token', [LiveTokenController::class, 'token']);
    });
    Route::post('/register', [\App\Http\Controllers\V1\AuthController::class, 'register']);
    Route::post('/login', [\App\Http\Controllers\V1\AuthController::class, 'login']);
    Route::post('/logout', [\App\Http\Controllers\V1\AuthController::class, 'logout'])->middleware('auth:sanctum');
});
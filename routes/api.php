<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LiveController;
use App\Http\Controllers\LiveTokenController;
use App\Http\Controllers\AgoraTokenController;

Route::prefix('v1')->group(function () {
    Route::get('/ping', function () {
        return response()->json(['status' => 'ok']);
    });
});
// $idRegex   = '[0-9]+';
// $slugRegex = '[0-9a-z\-]+';
Route::prefix('V1')->group(function () {

    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/lives', [LiveController::class, 'index']);
        Route::get('/getLive', [LiveController::class, 'getLive']);
        Route::post('/lives', [LiveController::class, 'store']);            // create live record
        Route::post('/lives/{live}/start', [LiveController::class, 'start']);
        
        Route::post('/lives/{live}/stop', [LiveController::class, 'stop']);
        Route::post('/lives/{live}/promote', [LiveController::class, 'promote']); // promote viewer -> host

        // Token endpoint (returns token based on role param)
        Route::get('/livesToken/{live}/token', [LiveController::class, 'token']);
        Route::get('/livesTokenController/{live}/token', [LiveTokenController::class, 'token']);
        Route::get('/agora/token', [AgoraTokenController::class, 'token']);

        // Route for Videos
        // Route::get('/videos', [\App\Http\Controllers\V1\VideoController::class, 'index']);

        // Routes for Posts
        Route::get('/posts', [\App\Http\Controllers\Api\V1\PostController::class, 'index']);
        Route::post('/posts', [\App\Http\Controllers\Api\V1\PostController::class, 'store']);
        Route::put('/posts/{post}', [\App\Http\Controllers\Api\V1\PostController::class, 'update']);
        Route::delete('/posts/{post}', [\App\Http\Controllers\Api\V1\PostController::class, 'destroy']);
    });
    Route::post('/register', [\App\Http\Controllers\Api\V1\AuthController::class, 'register']);
    Route::post('/login', [\App\Http\Controllers\Api\V1\AuthController::class, 'login']);
    Route::post('/logout', [\App\Http\Controllers\Api\V1\AuthController::class, 'logout'])->middleware('auth:sanctum');
});
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
$id = '[0-9a-z\-]+';
$video_id = '[0-9a-z\-]+';
$q = '[0-9a-z\-]+';
$userId = '[0-9a-z\-]+';
$replyId = $id;
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
        
        // Routes for Posts
        Route::get('/posts', [\App\Http\Controllers\Api\V1\PostController::class, 'index']);
        Route::post('/posts', [\App\Http\Controllers\Api\V1\PostController::class, 'store']);
        Route::put('/posts/{post}', [\App\Http\Controllers\Api\V1\PostController::class, 'update']);
        Route::delete('/posts/{post}', [\App\Http\Controllers\Api\V1\PostController::class, 'destroy']);

        // Routes for Videos
        Route::get('/videos', [\App\Http\Controllers\Api\V1\VideoController::class, 'index']);
        Route::post('/videos', [\App\Http\Controllers\Api\V1\VideoController::class, 'store']);
        Route::put('/videos/{id}', [\App\Http\Controllers\Api\V1\VideoController::class, 'update']);
        Route::delete('/videos/{id}', [\App\Http\Controllers\Api\V1\VideoController::class, 'destroy']);
        Route::post('/videos/{id}/share', [\App\Http\Controllers\Api\V1\VideoController::class, 'shareVideos']);
        Route::get('videos/search', [\App\Http\Controllers\Api\V1\VideoController::class, 'search']);
        // Routes for Likes
        Route::get('/likes', [\App\Http\Controllers\Api\V1\LikeController::class, 'index']);
        Route::post('/likes', [\App\Http\Controllers\Api\V1\LikeController::class, 'store']);
        Route::delete('/likes/{id}', [\App\Http\Controllers\Api\V1\LikeController::class, 'destroy']);
        Route::post('/likes/toggleLikeDislike', [\App\Http\Controllers\Api\V1\LikeController::class, 'toggleLikeDislike']);
        
        // Routes for Saves
        Route::get('/save', [\App\Http\Controllers\Api\V1\SaveController::class, 'index']);
        Route::post('/save', [\App\Http\Controllers\Api\V1\SaveController::class, 'store']);
        Route::delete('/save/{id}', [\App\Http\Controllers\Api\V1\SaveController::class, 'destroy']);
        Route::post('/save/toggleSaveUnSaved', [\App\Http\Controllers\Api\V1\SaveController::class, 'toggleSaveUnSave']);
        
        // Routes for Comments
        Route::get('videos/comments/{video_id}', [\App\Http\Controllers\Api\V1\CommentController::class, 'index']);
        Route::post('comments', [\App\Http\Controllers\Api\V1\CommentController::class, 'store']);
        Route::put('comments/{id}', [\App\Http\Controllers\Api\V1\CommentController::class, 'update']);
        Route::delete('comments/{id}', [\App\Http\Controllers\Api\V1\CommentController::class, 'destroy']);
        Route::post('likeUnlike/comments', [\App\Http\Controllers\Api\V1\CommentController::class, 'likeDislike']);
        Route::post('reply/comments', [\App\Http\Controllers\Api\V1\CommentController::class, 'addCommentReply']);
        Route::post('/follow/{userId}', [\App\Http\Controllers\Api\V1\FollowController::class, 'toggleFollow']);
        Route::get('/follow/{userId}/followers', [\App\Http\Controllers\Api\V1\FollowController::class, 'getFollowers']);
        Route::get('/follow/{userId}/following', [\App\Http\Controllers\Api\V1\FollowController::class, 'getFollowing']);
        Route::get('/profile/{userId}', [\App\Http\Controllers\Api\V1\ProfileController::class, 'getProfile']);
        // Route::get('/videos/{video_id}/comments', [\App\Http\Controllers\Api\V1\CommentController::class,'getComments']);
        Route::delete('/comment/reply/{replyId}', [\App\Http\Controllers\Api\V1\CommentController::class,'deleteReply']);
        // Route::post('/comments', [\App\Http\Controllers\Api\V1\CommentController::class,'store']);
    });
    Route::post('/register', [\App\Http\Controllers\Api\V1\AuthController::class, 'register']);
    Route::post('/login', [\App\Http\Controllers\Api\V1\AuthController::class, 'login']);
    Route::post('/logout', [\App\Http\Controllers\Api\V1\AuthController::class, 'logout'])->middleware('auth:sanctum');
});
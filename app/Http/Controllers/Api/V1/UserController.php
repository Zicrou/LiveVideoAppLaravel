<?php

namespace App\Http\Controllers\Api\V1;


use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Video;

use Laravel\Sanctum\PersonalAccessToken;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class UserController extends Controller implements HasMiddleware
{    public static function middleware()
    {
        return [
            new Middleware('auth:sanctum'),
        ];
    }

    public function toggleFollow(Request $request, $userId)
{
    $authUser = $request->user();

    if ($authUser->id === $userId) {
        return response()->json([
            'message' => 'You cannot follow yourself'
        ], 400);
    }

    $exists = DB::table('follows')
        ->where('follower_id', $authUser->id)
        ->where('following_id', $userId)
        ->exists();

    if ($exists) {
        DB::table('follows')
            ->where('follower_id', $authUser->id)
            ->where('following_id', $userId)
            ->delete();

        return response()->json([
            'following' => false
        ]);
    }

    DB::table('follows')->insert([
        'id' => Str::uuid(),
        'follower_id' => $authUser->id,
        'following_id' => $userId,
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    return response()->json([
        'following' => true
    ]);
}

}
    
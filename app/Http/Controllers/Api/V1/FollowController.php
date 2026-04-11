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

class FollowController extends Controller implements HasMiddleware
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
        $following = false;
    }else{
        DB::table('follows')->insert([
            'id' => Str::uuid(),
            'follower_id' => $authUser->id,
            'following_id' => $userId,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $following = true;
    }
    $user = User::withCount('followers', 'following')->find($userId);
    return response()->json([
        'following' => $following,
        'followersCount' => $user->followers_count,
        'followingCount' => $user->following_count
    ]);
}

    public function getFollowers($userId)
    {
        $followers = DB::table('follows')
            ->where('following_id', $userId)
            ->count();
            // ->join('users', 'follows.follower_id', '=', 'users.id')
            // ->select('users.id', 'users.name')
            // ->count();
            

        return response()->json([
            'followersCount' => $followers,
        ]);
    }
}
    
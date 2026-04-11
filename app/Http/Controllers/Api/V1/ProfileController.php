<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Like;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Video;

use Laravel\Sanctum\PersonalAccessToken;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ProfileController extends Controller implements HasMiddleware
{
      public static function middleware()
    {
        return [
            new Middleware('auth:sanctum'),
        ];
    }

    public function getProfile(Request $request, $userId)
    {
        $user = $request->user();
        if (!$user) {
            return response()->json([
                'message' => 'User not found'
            ], 404);
        }
        $userProfile = User::withCount('followers', 'following', 'likes')->find($userId);
        
        if (!$userProfile) {
            return response()->json([
                'message' => 'User not found'
            ], 404);
        }
        $videos = Video::query()
            ->where('owner_id', $userId)
            ->with("owner")
            ->withCount('likes')
            ->withCount([
                'likes as isLiked' => function ($query) use ($userId) {
                    $query->where('user_id', $userId);
                }
            ])
            ->withCount('saveds')
            ->withCount([
                'saveds as isSaved' => function ($query) use ($userId) {
                    $query->where('user_id', $userId);
                }
            ])->latest()
            ->withCount('comments')
            ->get();
            
            
        $likedVideos = Video::query()
            ->whereIn('id', function ($query) use ($userId) {
                $query->select('video_id')
                    ->from('likes')
                    ->where('user_id', $userId);
            })
            ->withCount('likes')
            ->withCount([
                'likes as isLiked' => function ($query) use ($userId) {
                    $query->where('user_id', $userId);
                }
            ])
            ->withCount('saveds')
            ->withCount([
                'saveds as isSaved' => function ($query) use ($userId) {
                    $query->where('user_id', $userId);
                }
            ])
            ->withCount('comments')
            ->latest()
            ->get();

            $savedVideos = Video::query()
            ->whereIn('id', function ($query) use ($userId) {
                $query->select('video_id')
                    ->from('saves')
                    ->where('user_id', $userId);
            })
            ->withCount('likes')
            ->withCount([
                'likes as isLiked' => function ($query) use ($userId) {
                    $query->where('user_id', $userId);
                }
            ])
            ->withCount('saveds')
            ->withCount([
                'saveds as isSaved' => function ($query) use ($userId) {
                    $query->where('user_id', $userId);
                }
            ])
            ->withCount('comments')
            ->latest()
            ->get();
            

        return response()->json([
            'user' => $userProfile,
            'followers_count' => $userProfile->followers()->count(),
            'following_count' => $userProfile->following()->count(),
            'likes_count' => $userProfile->likes()->count(),
            'videos' => $videos,
            'liked' => $likedVideos,
            'saved' => $savedVideos,
        ]);
    }

    
}

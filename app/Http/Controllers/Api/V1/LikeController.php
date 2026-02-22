<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Video;
use App\Models\Like;
use Laravel\Sanctum\PersonalAccessToken;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Routing\Controllers\HasMiddleware;
class LikeController extends Controller implements HasMiddleware
{
    public static function middleware()
    {
        return [
            new Middleware('auth:sanctum'),
        ];
    }
    public function index(Request $request){
        return[
            'likes' => Like::all()
        ];
    }

    public function toggleLikeDislike(Request $request){
        $user = $request->user();
        if(!$user){
            return response()->json([
                'message' => 'User not found'
            ], 404);
        }
        $data = $request->validate([
            'video_id' => 'required|uuid|exists:videos,id',
            'like_id' => 'uuid|nullable'
        ]);

        if($data['like_id'] == null){
            $this->store($user, $data['video_id']);
            return['message' => 'Video liked successfully',
            // 'like' => $like
            ];
        }else{
            $this->destroy($data['like_id'], $user);
            return['message' => 'Video unliked successfully',];
        }
    }

    public function store($user, $video){
       
        $like = Like::create([
            // 'owner_id' => $user->id,
            'user_id' => $user->id,
            'video_id' => $video,
            ]);
                    
        return[
            'message' => 'Video liked successfully',
            'like' => $like,
        ];
    }

    public function destroy($likeId, $user){
       
        $like = Like::find($likeId);
        if(!$like){
            return response()->json([
                'message' => 'Like not found'
            ], 404);
        }
        if($like->user_id !== $user->id){
            return response()->json([
                'message' => 'Unauthorized'
            ], 403);
        }
        $like->delete();
        return[
            'message' => 'Video unliked successfully',
        ];
    }
}

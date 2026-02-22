<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Video;
use App\Models\Like;
use App\Models\Comment;
use Laravel\Sanctum\PersonalAccessToken;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Routing\Controllers\HasMiddleware;
class CommentController extends Controller implements HasMiddleware
{
     public static function middleware()
    {
        return [
            new Middleware('auth:sanctum'),
        ];
    }

    public function index(Request $request){
        $user = $request->user();
        if(!$user){
            return response()->json([
                'message' => 'User not found'
            ], 404);
        }
        $data = $request->validate([
            'video_id' => ['required', 'uuid','exists:videos,id'],
            // 'comment' => 'required|string'
        ]);
        $comments = Comment::where('video_id',$data['video_id'])->get();
        return[
            'comments' => $comments,
            'commentsCount' => $comments->count(),
        ];
    }

    public function store(Request $request){
        // Logic to like a video
        $user = $request->user();
        if(!$user){
            return response()->json([
                'message' => 'User not found'
            ], 404);
        }
        $data = $request->validate([
            'video_id' => ['required', 'uuid','exists:videos,id'],
            'comment' => 'required|string'
        ]);
        $video = Video::where('id',$data['video_id'])->with('comments')->withCount('comments')->first();
        // return['video' => $video->id];
        $comment = Comment::create([
            // 'owner_id' => $user->id,
            'user_id' => $user->id,
            'video_id' => $video->id,
            'comment' => $data['comment']
        ]);

        return[
            'message' => 'Video liked successfully',
            'token' => $request->bearerToken(),
            'user' => $user,
            'comment' => $comment,
            'commentCount' => $video->comments_count
        ];
    }

    public function update(Request $request, $commentId){
         $user = $request->user();
        if(!$user){
            return response()->json([
                'message' => 'User not found'
            ], 404);
        }

        $data = $request->validate([
            'video_id' => ['required', 'uuid','exists:videos,id',],
            'comment' => ['required', 'string']
            ]);
            
        $comment = Comment::find($commentId);
        // return['data' => $data];
        
        if($comment->user_id !== $user->id){
            return response()->json([
                'message' => 'Unauthorized'
            ], 403);
        }
        $comment->update([
            'user_id' => $user->id,
            // 'video_id' => $video->id,
            'comment' => $data['comment']
        ]);

        return[
            'comment' => $comment,
        ];
    }

    public function destroy(Request $request, $commentId){
        $user = $request->user();
        // if(!$user){
        //     return response()->json([
        //         'message' => 'User not found'
        //     ], 404);
        // }
            
        $comment = Comment::find($commentId);
        // return['data' => $data];
        
        if(!$user && $comment->user_id !== $user->id){
            return response()->json([
                'message' => 'Unauthorized'
            ], 403);
        }
        $comment->delete();
        return[
            'comment' => $comment
        ];
    }
}
<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Video;
use App\Models\Like;
use App\Models\Comment;
use App\Models\CommentLike;
use Laravel\Sanctum\PersonalAccessToken;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Support\Facades\Validator;

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
        // return ['user' => $user];
        if(!$user){
            return response()->json([
                'message' => 'User not found'
            ], 404);
        }

        $validator = Validator::make(
        ['video_id' => $request->video_id],
        [
            'video_id' => 'required|uuid|exists:videos,id'
        ]
    );

    if ($validator->fails()) {
        return response()->json([
            'message' => 'Invalid video id',
            'errors' => $validator->errors()
        ], 422);
    }
        $comments = Comment::with(['user', 'replies.user'])
        ->withCount('likes')
        ->withCount([
                'likes as isLiked' => function ($query) use ($user) {
                    $query->where('user_id', $user->id);
                }
            ])
        ->where('video_id', $request->video_id)
        ->whereNull('parent_id')
        ->latest()
        ->get();
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
            'comment' => $data['comment'],
            'parent_id' => $request->parent_id
        ]);

        return[
            'message' => 'Video liked successfully',
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
        if(!$user){
            return response()->json([
                'message' => 'User not found'
            ], 404);
        }
            
        $comment = Comment::find($commentId);
        if(!$comment){
            return response()->json([
                'message' => 'Comment not found'
            ], 404);
        }
        if($comment->user_id !== $user->id){
            return response()->json([
                'message' => 'Unauthorized',
            ], 403);
        }
        $comment->replies()->delete();
        $comment->delete();
        return[
            'message' => 'Comment deleted successfully',
            'status' => 'deleted',
            'deletedCommentId' => true
        ];
    }

    public function likeDislike(Request $request){

        $user = $request->user();
        if(!$user){
            return response()->json([
                'message' => 'User not found'
            ], 404);
        }
        $data = $request->validate([
            'comment_id' => ['required', 'exists:comments,id', 'uuid']
        ]);
        $likeComment = CommentLike::where('user_id', $user->id)->where('comment_id', $data['comment_id'])->first();
        // return ["like" => $like];
        if(!$likeComment){
            $this->storeCommentLike($user, $data['comment_id']);
            return['message' => 'Comment liked successfully',
            ];
        }else{
            $likeComment->delete();
            return['message' => 'Comment unliked successfully',];
        }
    }

    public function storeCommentLike($user, $commentId){
       
        $commentLike = CommentLike::create([
            // 'owner_id' => $user->id,
            'user_id' => $user->id,
            'comment_id' => $commentId,
            ]);
                    
        return[
            'commentLike' => $commentLike,
        ];
    }

    public function addCommentReply(Request $request){
        $user = $request->user();
        if(!$user){
            return response()->json([
                'message' => 'User not found'
            ], 404);
        }
        $data = $request->validate([
            'video_id' => ['required', 'uuid','exists:videos,id'],
            'comment' => 'required|string',
            'parent_id' => ['required', 'uuid', 'exists:comments,id']
        ]);
        $video = Video::where('id',$data['video_id'])->with('comments')->withCount('comments')->first();
        // return['video' => $video->id];
        $reply = Comment::create([
            // 'owner_id' => $user->id,
            'user_id' => $user->id,
            'video_id' => $data['video_id'],
            'comment' => $data['comment'],
            'parent_id' => $data['parent_id']
        ]);

        return[
            'message' => 'Replied successfully',
            'comment' => $reply,
            'commentCount' => $video->comments_count
        ];

    }

    public function deleteReply(Request $request, $replyId){
        $user = $request->user();
        if(!$user){
            return response()->json([
                'message' => 'User not found'
            ], 404);
        }
            
        $comment = Comment::find($replyId);
        if(!$comment){
            return response()->json([
                'message' => 'Comment not found'
            ], 404);
        }
        if($comment->user_id !== $user->id){
            return response()->json([
                'message' => 'Unauthorized',
            ], 403);
        }
        $comment->delete();
        return[
            'message' => 'Reply deleted successfully',
            'status' => 'deleted',
            'deletedReplyId' => true
        ];
    }
}
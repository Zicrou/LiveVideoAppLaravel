<?php

namespace App\Http\Controllers\Api\V1;


use App\Http\Controllers\Controller;
use App\Http\Resources\PostResource;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Post;
use Laravel\Sanctum\PersonalAccessToken;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Routing\Controllers\HasMiddleware;

class PostController extends Controller implements HasMiddleware
{
    public static function middleware()
    {
        return [
            new Middleware('auth:sanctum'),
        ];
    }
    public function index(Request $request){
        $userOwner = $request->user();
        return PostResource::collection(Post::where('owner_id', $userOwner->id)->with('owner')->get());
    }

    public function store(Request $request){
        // 0: video, 1: image, 2: text
        $request->validate([
            'post_type' => 'required|in:text,image,video',
        ]);
        $post = Post::create([
            'post_type' => $request->post_type,
            'owner_id' => $request->user()->id,
        ]);

        return response()->json([
            'message' => 'Post created successfully',
            'post' => $post
        ], 201);
    }

    public function show(Request $request, Post $post){
        return new PostResource($post);
    }

    public function update(Request $request, Post $post){
        $userOwner = $request->user();
        if($post->owner_id !== $userOwner->id){
            return response()->json([
                'message' => 'Unauthorized'
            ], 403);
        }
        $request->validate([
            'post_type' => 'required|in:text,image,video',
        ]);
        $post->update([
            'post_type' => $request->post_type,
        ]);

        return response()->json([
            'message' => 'Post updated successfully',
            'post' => $post
        ], 200);
    }


    public function destroy(Request $request, Post $post){
        $userOwner = $request->user();
        if($post->owner_id !== $userOwner->id){
            return response()->json([
                'message' => 'Unauthorized'
            ], 403);
        }
        $post->delete();

        return response()->json([
            'message' => 'Post deleted successfully',
        ], 200);
    }

}

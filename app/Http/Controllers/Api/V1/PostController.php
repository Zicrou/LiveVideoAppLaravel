<?php

namespace App\Http\Controllers\Api\V1;


use App\Http\Controllers\Controller;
use App\Http\Resources\PostResource;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Post;
use App\Models\Video;
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
        return PostResource::collection(Post::where('owner_id', $userOwner->id)->with(['owner', 'video'])->get());
    }

    public function store(Request $request){
        $userExists = User::find($request->user()->id);
        if(!$userExists){
            return response()->json([
                'message' => 'User not found'
            ], 404);
        }

        $data = $request->validate([
            'post_type' => 'required|in:text,image,video',
            'video_url' => 'required|string',
            'caption' => 'string|nullable',
        ]);
        
        $post = Post::create([
            'post_type' => $request->post_type,
            'owner_id' => $request->user()->id,
            ]);
            
        $video = Video::create([
            'caption' => $request->caption,
            'video_url' => $request->video_url,
            'owner_id' => $userExists->id,
            'post_id' => $post->id,
            
            ]);

        return response()->json([
            'message' => 'Post created successfully',
            'post' => $post,
            'video' => $video,
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
            'video_url' => 'string|nullable',
            'image_url' => 'string|nullable',
            'text' => 'string|nullable',
            'caption' => 'string|nullable',
        ]);
        $post->update([
            'post_type' => $request->post_type,
        ]);

        $video = $post->video;
        if($video){
            $video->update([
                'caption' => $request->caption,
                'video_url' => $request->video_url,
            ]);
        }

        return response()->json([
            'message' => 'Post updated successfully',
            'post' => $post,
            'video' => $video,
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

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
        return PostResource::collection(Post::with(['video'])->get());
    }

    public function store(Request $request){
        $userExists = User::find($request->user()->id);
        if(!$userExists){
            return response()->json([
                'message' => 'User not found'
            ], 404);
        }

        $data = $request->validate([
            'post_type' => ['required', 'in:text,image,video'],
            'video_url' => ['nullable', 'string'],
            'caption' => ['string', 'nullable'],
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
            'post_type' => ['required', 'in:text,image,video'],
            'video_url' => ['string', 'nullable'],
            'image_url' => ['string', 'nullable'],
            'text' => ['string', 'nullable'],
            'caption' => ['string', 'nullable'],
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
        } elseif($request->post_type === 'video' && !$video){
            $video = Video::create([
                'caption' => $request->caption,
                'video_url' => $request->video_url,
                'owner_id' => $userOwner->id,
                'post_id' => $post->id,
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
        $deleteVideoOnly = $request->validate(['delete_video' => ['boolean']]);
        if($deleteVideoOnly){
            $video = $post->video;
            if($video){
                $video->delete();
            }
            return response()->json([
                'message' => 'Video deleted successfully',
                'video' => $video,
            ], 200);
        }
        // Delete the whole post and its related video
        Post::destroy($post->id);

        // If you want to delete the video separately, you can do it like this:
        // $video = $post->video;
        // if($video){
        //     $video->delete();
        // }

        // Delete one or many images related to the post if needed
        // $images = $post->images; // Assuming you have an images relationship
        // foreach ($images as $image) {
        //     $image->delete();
        // }


        return response()->json([
            'message' => 'Post deleted successfully',
            'post' => $post->id,
        ], 200);
    }
}

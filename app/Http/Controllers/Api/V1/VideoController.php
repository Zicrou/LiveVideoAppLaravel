<?php

namespace App\Http\Controllers\Api\V1;


use App\Http\Controllers\Controller;
use App\Http\Requests\VideoFormRequest;
use App\Http\Resources\LikeResource;
use App\Http\Resources\PostResource;
use App\Http\Resources\VideoResource;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Post;
use App\Models\Video;
use App\Models\Like;

use Laravel\Sanctum\PersonalAccessToken;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Routing\Controllers\HasMiddleware;

class VideoController extends Controller implements HasMiddleware
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
            return ['message' => "User not found"];
        }
        return[
            "videos" => Video::query()
            ->with('likes')
            ->withCount('likes')
            ->with('saveds')
            ->withCount('saveds')
            ->with('comments')
            ->withCount('comments')
            ->get(),
        ];
        // return VideoResource::collection(Video::withCount('likes')->with('likes')->get());
    }

    public function store(VideoFormRequest $request){
        $userExists = User::find($request->user()->id);
        if(!$userExists){
            return response()->json([
                'message' => 'User not found'
            ], 404);
        }

        $data = $request->validated();
            
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

    public function update(VideoFormRequest $request, $postId){
        $userOwner = $request->user();
        $post = Post::find($postId);
        if($post->owner_id !== $userOwner->id){
            return response()->json([
                'message' => 'Unauthorized'
                ], 403);
                }
        // return['post' => $postId];  
        $request->validated();
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


    public function destroy(Request $request, $postId){
        $userOwner = $request->user();
        $post = Post::find($postId);
        // return['user' => $userOwner, "post" => $post,
        // "request" => $postId];
        if($post->owner_id !== $userOwner->id){
            return response()->json([
                'message' => 'Unauthorized'
            ], 403);
        }

        Post::destroy($post->id);
        return response()->json([
            'message' => 'Post deleted successfully',
        ], 200);

        // $post->delete();
        // $deleteVideoOnly = $request->validate(['delete_video' => 'boolean']);
        // if($deleteVideoOnly){
        //     $video = $post->video;
        //     if($video){
        //         $video->delete();
        //     }
        //     return response()->json([
        //         'message' => 'Video deleted successfully',
        //         'video' => $video,
        //     ], 200);
        // }
        // Delete the whole post and its related video

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


        
    }
}

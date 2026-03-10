<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\LikeFormRequest;
use App\Http\Requests\SaveFormRequest;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Video;
use App\Models\Like;
use App\Models\Save;
use Laravel\Sanctum\PersonalAccessToken;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Routing\Controllers\HasMiddleware;
class SaveController extends Controller implements HasMiddleware
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
            'saves' => Save::all()
        ];
    }

    public function toggleSaveUnSave(SaveFormRequest $request){
        $user = $request->user();
        if(!$user){
            return response()->json([
                'message' => 'User not found'
            ], 404);
        }
        $data = $request->validated();
        $save = Save::where('user_id', $user->id)->where('video_id', $data['video_id'])->first();
        // return['save' => $save];
        if(!$save){
            // return [$data['save_id']];
            return $this->store($user, $data['video_id']);
            
        }
        $save->delete();
        return[
            'message' => 'Video unsaved successfully',
        ];
    }

    public function store($user, $video){
       
        $save = Save::create([
            // 'owner_id' => $user->id,
            'user_id' => $user->id,
            'video_id' => $video,
            ]);
                    
        return[
            'message' => 'Video saved successfully',
            'save' => $save,
        ];
    }

    public function destroy($saveId, $user){
       
        $save = Save::find($saveId);
        if(!$save){
            return response()->json([
                'message' => 'Save not found'
            ], 404);
        }
        if($save->user_id !== $user->id){
            return response()->json([
                'message' => 'Unauthorized'
            ], 403);
        }
        $save->delete();
        return[
            'message' => 'Video unsaved successfully',
        ];
    }
}

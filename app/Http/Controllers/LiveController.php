<?php
// Use Postman to test these API endpoints
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Live;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Laravel\Sanctum\PersonalAccessToken;


class LiveController extends Controller
{
    // List lives (basic)
    public function index()
    {
        $lives = Live::orderBy('created_at', 'desc')->get();
        return response()->json($lives);
    }

    // Create a new live room (only host can create)
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'nullable|string|max:255',
        ]);

        $channelName = 'live_' . Str::random(10); // or derive using user/id
        $live = Live::create([
            'channel_name' => $channelName,
            'host_user_id' => $request->user()->id,
            'title' => $request->input('title'),
            'status' => 'scheduled',
        ]);

        return response()->json($live, 201);
    }

    // Start live: mark live and optionally start CDN push via Agora REST
    public function start(Request $request, Live $live)
    {
        // Get the User using the old method
        // $tokenFromRequest = PersonalAccessToken::findToken($request->bearerToken());
        // $user = $tokenFromRequest->tokenable;

        // authorize: only host or admin
        if ($request->user()->id !== $live->host_user_id) {
            return response()->json(['error' => 'Forbidden'], 403);
        }

        $live->status = 'live';
        $live->started_at = now();
        $live->save();

        // Optionally: start pushing to CDN (call helper)
        // If you plan to use Agora Media Push to CDN, call the helper:
        if ($request->filled('cdn_push_url')) {
            $live->cdn_push_url = $request->input('cdn_push_url'); // store for stop later
            $live->save();

            try {
                app('App\Services\AgoraCdnService')->startPush($live->channel_name, $live->cdn_push_url);
            } catch (\Exception $e) {
                Log::error('Failed to start CDN push: '.$e->getMessage());
            }
        }
        
        // Return live with token, Need to store it dans la base données, table live
       // $tokenLive = $this->token($request, $live);
        //$live['liveToken'] = $tokenLive->original;
        
        return response()->json($live);
        // return response()->json($request->user(), 200);
    }

    // Stop live: stop push and mark ended
    public function stop(Request $request, Live $live)
    {
        if ($request->user()->id !== $live->host_user_id) {
            return response()->json(['error' => 'Forbidden'], 403);
        }

        try {
            if ($live->cdn_push_url) {
                app('App\Services\AgoraCdnService')->stopPush($live->channel_name, $live->cdn_push_url);
            }
        } catch (\Exception $e) {
            Log::error('Failed stop push: '.$e->getMessage());
        }

        $live->status = 'ended';
        $live->ended_at = now();
        $live->save();

        return response()->json($live);
    }

    // Promote viewer -> host
    public function promote(Request $request, Live $live)
    {
        // Example policy: current host must allow promotion or it's an owner action
        // For simplicity: only owner can transfer host role
        if ($request->user()->cannot('promote', $live)) {
            return response()->json(['error'=>'Forbidden'],403);
        }

        $newHostId = $request->input('user_id');
        $live->host_user_id = $newHostId;
        $live->save();

        return response()->json($live);
    }

    public function token(Request $request, Live $live)
    {
        $role = $request->query('role', 'viewer'); // 'host' or 'viewer'
        $uid = $request->user()->id ?? 0;

        // Option A: call a Node token service you run locally / private (recommended)
        $nodeTokenServer = config('services.token_server.url'); // e.g. http://127.0.0.1:8080/token

        if ($nodeTokenServer) {
            $resp = Http::get($nodeTokenServer, [
                'channel' => $live->channel_name,
                'uid' => $uid,
                'role' => $role === 'host' ? 'host' : 'viewer',
            ]);

            return response()->json($resp->json("token"));
        }

        // Option B: If you have a PHP token generator, swap here
        // Example : $token = app('App\Services\AgoraTokenService')->buildToken($live->channel_name, $uid, $role);
        // return response()->json(['token' => $token]);

        return response()->json(['error' => 'No token server configured'], 500);
    }
}
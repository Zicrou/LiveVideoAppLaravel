<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Live;
use Illuminate\Support\Facades\Http;

class LiveTokenController extends Controller
{
    public function token(Request $request, Live $live)
    {
        $role = "host"; //$request->query('role', 'host'); // 'host' or 'viewer'
        $uid = $request->user()->id ?? 0;

        // Option A: call a Node token service you run locally / private (recommended)
        $nodeTokenServer = config('services.token_server.url'); // e.g. http://127.0.0.1:8080/token

        if ($nodeTokenServer) {
            $resp = Http::get($nodeTokenServer, [
                'channel' => $live->channel_name,
                'uid' => $uid,
                
                'role' => $role === 'host' ? 'host' : 'viewer',
            ]);

            return response()->json($resp);
        }

        // Option B: If you have a PHP token generator, swap here
        // Example : $token = app('App\Services\AgoraTokenService')->buildToken($live->channel_name, $uid, $role);
        // return response()->json(['token' => $token]);

        return response()->json(['error' => 'No token server configured'], 500);
    }
}
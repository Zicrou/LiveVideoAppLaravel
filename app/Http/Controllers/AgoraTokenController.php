<?php

namespace App\Http\Controllers;

// use App\Services\Agora\RtcTokenBuilder;
use CyberDeep\LaravelAgoraTokenGenerator\Services\Token\RtcTokenBuilder2;
use Illuminate\Http\Request;
use Laravel\Sanctum\PersonalAccessToken;
use Nette\Utils\Json;
// use BoogieFromZk\AgoraToken\RtcTokenBuilder2;
// use Yasser\Agora\RtcTokenBuilder;

class AgoraTokenController extends Controller
{
    
public function token(Request $request)
{
        $appID = env('AGORA_APP_ID');
    $appCertificate = env('AGORA_APP_CERTIFICATE');

    $channelName = $request->channelName ?? "test_channel";
    $user = 0;
    $role = RtcTokenBuilder2::ROLE_PUBLISHER;
    $expireTimeInSeconds = 3600;
    $currentTimestamp = now()->getTimestamp();
    $privilegeExpiredTs = $currentTimestamp + $expireTimeInSeconds;

    $tokenPublisher = RtcTokenBuilder2::buildTokenWithUid($appID, $appCertificate, $channelName, $user, $role, $privilegeExpiredTs);


   $roleSubscriber = RtcTokenBuilder2::ROLE_SUBSCRIBER;
    $tokenSubscriber = RtcTokenBuilder2::buildTokenWithUid($appID, $appCertificate, $channelName, $user, $roleSubscriber, $privilegeExpiredTs);

    return response()->json([
        'tokenPublisher' => $tokenPublisher,
        'tokenSubscriber' => $tokenSubscriber,
        'expire_at' => $privilegeExpiredTs,
        'ChannelName' => $channelName
    ]);
}
}
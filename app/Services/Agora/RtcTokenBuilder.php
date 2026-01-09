<?php

namespace App\Services\Agora;

class RtcTokenBuilder
{
    const ROLE_PUBLISHER = 1;
    const ROLE_SUBSCRIBER = 2;

    public static function buildTokenWithUid(
        $appId,
        $appCertificate,
        $channelName,
        $uid,
        $role,
        $privilegeExpireTs
    ) {
        return self::buildToken(
            $appId,
            $appCertificate,
            $channelName,
            $uid,
            $role,
            $privilegeExpireTs
        );
    }

    private static function buildToken(
        $appId,
        $appCertificate,
        $channelName,
        $uid,
        $role,
        $privilegeExpireTs
    ) {
        $token = new AccessToken(
            $appId,
            $appCertificate,
            $channelName,
            $uid
        );
        $token->addPrivilege(AccessToken::PRIVILEGE_JOIN_CHANNEL, $privilegeExpireTs);

        if ($role == self::ROLE_PUBLISHER) {
            $token->addPrivilege(AccessToken::PRIVILEGE_PUBLISH_AUDIO_STREAM, $privilegeExpireTs);
            $token->addPrivilege(AccessToken::PRIVILEGE_PUBLISH_VIDEO_STREAM, $privilegeExpireTs);
            $token->addPrivilege(AccessToken::PRIVILEGE_PUBLISH_DATA_STREAM, $privilegeExpireTs);
        }

        return $token->build();
    }
}
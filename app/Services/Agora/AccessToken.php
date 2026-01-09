<?php

namespace App\Services\Agora;

class AccessToken
{
    const PRIVILEGE_JOIN_CHANNEL = 1;
    const PRIVILEGE_PUBLISH_AUDIO_STREAM = 2;
    const PRIVILEGE_PUBLISH_VIDEO_STREAM = 3;
    const PRIVILEGE_PUBLISH_DATA_STREAM = 4;

    private $appId;
    private $appCertificate;
    private $channelName;
    private $uid;
    private $messages = [];

    public function __construct($appId, $appCertificate, $channelName, $uid)
    {
        $this->appId = $appId;
        $this->appCertificate = $appCertificate;
        $this->channelName = $channelName;
        $this->uid = $uid;
    }

    public function addPrivilege($privilege, $expireTimestamp)
    {
        $this->messages[$privilege] = $expireTimestamp;
    }

    public function build()
    {
        return base64_encode(json_encode([
            'appId' => $this->appId,
            'channelName' => $this->channelName,
            'uid' => $this->uid,
            'privileges' => $this->messages,
        ]));
    }
}
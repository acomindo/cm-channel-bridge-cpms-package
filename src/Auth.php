<?php

namespace ChannelBridge\Cpms;

class Auth extends Base
{
    public function __construct($timeout=10)
    {
        parent::__construct($timeout);
    }

    public function get($url, $username, $apiKey)
    {
        $res = $this->request('POST', $url, [
            'json' => [
                'auth' => [
                    'apiKeyCredentials' => [
                        'username' => $username,
                        'apiKey' => $apiKey
                    ]
                ]
            ]
        ]);

        return $res;
    }
}
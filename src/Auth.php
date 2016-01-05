<?php

namespace aCommerce\CMBridgeSquid;

class Auth extends CMPS implements AuthInterface
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
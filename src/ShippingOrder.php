<?php

namespace ChannelBridge\Cpms;

class ShippingOrder extends Base
{
    public $timeout;
    public $client;

    public function __construct($timeout=10)
    {
        parent::__construct($timeout);
    }

    public function create($tokenId, $url, $json)
    {
        $res = $this->request('PUT', $url, [
            'json' => $json,
            'headers' => ['X-Subject-Token' => $tokenId]
        ]);

        return $res;
    }

    public function get($tokenId, $url)
    {
        $res = $this->request('GET', $url, [
            'headers' => [
                'X-Subject-Token' => $tokenId
            ]
        ]);

        return $res;
    }
}
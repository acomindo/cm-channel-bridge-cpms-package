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
            'body' => $json,
            'headers' => ['X-Subject-Token' => $tokenId, 'Content-Type' => 'application/json']
        ]);

        return $res;
    }

    public function update($tokenId, $url, $json)
    {
        $res = $this->request('PATCH', $url, [
            'body' => $json,
            'headers' => ['X-Subject-Token' => $tokenId, 'Content-Type' => 'application/json']
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
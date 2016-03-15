<?php

namespace ChannelBridge\Cpms;

class InventoryAllocation extends Base
{
    public $client;
    protected $timeout;

    public function __construct($timeout=10)
    {
        parent::__construct($timeout);
    }

    public function get($tokenId, $url)
    {
        $res = $this->request("GET", $url, [
            'headers' => [
                'X-Subject-Token' => $tokenId
            ]
        ]);

        if ($res['code'] === 200 && isset($res['header']['Link'][0])) {
            $refmt = "Link: ".$res['header']['Link'][0];
            $result = \IndieWeb\http_rels($refmt);
            $res['next'] = $result['next'][0];
        }

        return $res;
    }
}

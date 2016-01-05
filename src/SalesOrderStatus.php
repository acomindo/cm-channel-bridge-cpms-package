<?php

namespace Acommerce\Cmp;

class SalesOrderStatus extends Cmp
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

        return $res;

    }
}
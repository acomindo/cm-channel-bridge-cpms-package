<?php

namespace aCommerce\CMBridgeSquid;

interface SalesOrderInterface
{
    /**
     * @param string $url
     * @param string $tokenId
     * @param array $order
     * @return array
     */
    public function create($url, $tokenId, $order);
}

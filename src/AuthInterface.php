<?php

namespace aCommerce\CMBridgeSquid;

interface AuthInterface
{
    /**
     * @param string $url
     * @param string $username
     * @param string $apiKey
     * @return mixed
     */
    public function getTokenId($url, $username, $apiKey);
}
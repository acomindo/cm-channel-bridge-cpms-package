<?php

namespace aCommerce\CMBridgeSquid;

interface AuthInterface
{
    /**
     * @param string $url
     * @param string $username
     * @param string $apiKey
     * @return array
     */
    public function get($url, $username, $apiKey);
}
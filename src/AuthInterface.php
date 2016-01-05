<?php

namespace Acp\Cmp;

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
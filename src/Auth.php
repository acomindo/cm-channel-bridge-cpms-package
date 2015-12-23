<?php

namespace aCommerce\CMBridgeSquid;


use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Exception\ServerException;

class Auth implements AuthInterface
{
    protected $client;

    public function __construct()
    {
        $this->client = new Client();
    }

    public function getTokenId($url, $username, $apiKey)
    {
        try {
            $request = $this->client->request('POST', $url, [
                'json' => [
                    'auth' => [
                        'apiKeyCredentials' => [
                            'username' => $username,
                            'apiKey' => $apiKey
                        ]
                    ]
                ],
                'timeout' => 5
            ]);

            $response = [
                'message' => 'success',
                'code' => $request->getStatusCode(),
                'data' => json_decode($request->getBody(), true)['token']
            ];
        } catch (RequestException $e) {
            $response = [
                'message' => $e->getMessage(),
                'code' => $e->getCode()
            ];
        }

        return $response;
    }
}
<?php

namespace aCommerce\CMBridgeSquid;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\ServerException;

class Auth implements AuthInterface
{
    public $timeout;
    public $client;

    /**
     * Auth constructor.
     * @param int $timeout connection timeout
     */
    public function __construct($timeout=5)
    {
        $this->client = new Client();
        $this->timeout = $timeout;
    }

    /**
     * @param string $url
     * @param string $username
     * @param string $apiKey
     * @return array
     */
    public function get($url, $username, $apiKey)
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
                'timeout' => $this->timeout
            ]);

            $response = [
                'message' => 'success',
                'code' => $request->getStatusCode(),
                'data' => json_decode($request->getBody(), true)
            ];
        } catch (ClientException $e) {
            $response = [
                'message' => $e->getResponse()->getReasonPhrase(),
                'code' => $e->getResponse()->getStatusCode()
            ];
        } catch (ConnectException $e) {
            $handleContext = $e->getHandlerContext();
            $response = [
                'message' => $handleContext['error'],
                'code' => $handleContext['http_code']
            ];
        } catch (ServerException $e) {
            $response = [
                'message' => $e->getResponse()->getReasonPhrase(),
                'code' => $e->getResponse()->getStatusCode()
            ];
        }

        return $response;
    }
}
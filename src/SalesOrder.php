<?php

namespace aCommerce\CMBridgeSquid;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\ServerException;

class SalesOrder implements SalesOrderInterface
{
    public $timeout;
    public $client;

    /**
     * SalesOrder constructor.
     * @param int $timeout
     */
    public function __construct($timeout=10)
    {
        $this->client = new Client();
        $this->timeout = $timeout;
    }

    /**
     * @param string $url
     * @param string $tokenId
     * @param array $order
     * @return array
     */
    public function create($url, $tokenId, $order)
    {
        try {
            $request = $this->client->request('PUT', $url, [
                'json' => $order,
                'timeout' => $this->timeout,
                'headers' => [
                    'X-Subject-Token' => $tokenId
                ]
            ]);

            $response = [
                'message' => 'success',
                'code' => $request->getStatusCode(),
                'body' => json_decode($request->getBody(), true)
            ];
        } catch (ClientException $e) {
            $response = [
                'message' => $e->getResponse()->getReasonPhrase(),
                'body' => json_decode($e->getResponse()->getBody(), true),
                'code' => $e->getResponse()->getStatusCode()
            ];
        } catch (ServerException $e) {
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
        }

        return $response;
    }
}
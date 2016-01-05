<?php

namespace Acommerce\Cmp;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\ServerException;
use GuzzleHttp\Exception\ClientException;

class SalesOrderStatus
{
    public $client;
    public $baseUrl;
    public $timeout;

    private $queryString;
    private $tokenId;
    private $url;

    public function __construct(
        $baseUrl,
        $since = null,
        $orderStatus = null,
        array $orderId = [],
        $timeout = 10
    ) {
        $this->client = new Client();
        $this->timeout = $timeout;
        $this->baseUrl = $baseUrl;

        $this->queryString($since, $orderStatus, $orderId);
    }

    private function queryString($since, $orderStatus, $orderId)
    {
        $query = [];
        if ($since) {
            $this->since($since);
            $query['since'] = $since;
        }

        if ($orderStatus) {
            $this->orderStatus($orderStatus);
            $query['orderStatus'] = $orderStatus;
        }

        if ($query) {
            $this->queryString = '?' . $this->toQueryString($query);
        }

        if ($orderId) {
            $this->orderId($orderId);
        }
    }

    public function getChannel($tokenId, $channelId)
    {
        $this->url = $this->baseUrl . 'channel/' . $channelId . '/sales-order-status';
        $this->tokenId = $tokenId;

        return $this->getResponse();
    }

    public function getPartner($tokenId, $partnerId)
    {
        $this->url = $this->baseUrl . 'partner/' . $partnerId . '/sales-order-status';
        $this->tokenId = $tokenId;

        return $this->getResponse();
    }

    private function getResponse()
    {
        try {
            $response = $this->request();
        } catch (ClientException $e) {
            $response = $this->clientException($e);
        } catch (ServerException $e) {
            $response = $this->serverException($e);
        } catch (ConnectException $e) {
            $response = $this->connectException($e);
        }

        return $response;
    }

    private function clientException(ClientException $e)
    {
        return [
            'message' => $e->getResponse()->getReasonPhrase(),
            'body' => json_decode($e->getResponse()->getBody(), true),
            'code' => $e->getResponse()->getStatusCode()
        ];
    }

    private function serverException(ServerException $e)
    {
        return [
            'message' => $e->getResponse()->getReasonPhrase(),
            'code' => $e->getResponse()->getStatusCode()
        ];
    }

    private function connectException(ConnectException $e)
    {
        $handleContext = $e->getHandlerContext();
        return [
            'message' => $handleContext['error'],
            'code' => $handleContext['http_code']
        ];
    }

    private function request()
    {
        if ($this->queryString) {
            $this->url .= $this->queryString;
        }

        $request = $this->client->request('GET', $this->url, [
            'timeout' => $this->timeout,
            'headers' => [
                'X-Subject-Token' => $this->tokenId
            ]
        ]);

        return [
            'message' => 'success',
            'code' => $request->getStatusCode(),
            'body' => json_decode($request->getBody(), true)
        ];
    }

    private function since($since) {
        $this->queryString = 'since=' . $since;
    }

    private function orderStatus($orderStatus) {
        $this->queryString = 'orderStatus=' . $orderStatus;
    }

    private function toQueryString(array $array)
    {
        $query = http_build_query($array, null, '&');

        return preg_replace('/%5B(?:[0-9]|[1-9][0-9]+)%5D=/', '=', $query);
    }


    private function orderId(array $orderId)
    {
        $this->queryString = '/id?' . $this->toQueryString(['id'=>$orderId]);
    }
}
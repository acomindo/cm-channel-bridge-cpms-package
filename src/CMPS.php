<?php

namespace aCommerce\CMBridgeSquid;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\ServerException;

class CMPS
{
    public $client;

    protected $timeout;

    public function __construct($timeout=10)
    {
        $this->client = new Client();
        $this->timeout = $timeout;
    }

    protected function request($method, $url, $options=[])
    {
        $options = array_merge_recursive(['timeout' => $this->timeout], $options);

        try {
            $req = $this->client->request($method, $url, $options);

            $res = [
                'message' => 'success',
                'code' => $req->getStatusCode(),
                'body' => json_decode($req->getBody(), true)
            ];
        } catch (ClientException $e) {
            $res = [
                'message' => $e->getResponse()->getReasonPhrase(),
                'code' => $e->getResponse()->getStatusCode(),
                'body' => json_decode($e->getResponse()->getBody(), true),
            ];
        } catch (ConnectException $e) {
            $handleContext = $e->getHandlerContext();
            $res = [
                'message' => $handleContext['error'],
                'code' => $handleContext['http_code']
            ];
        } catch (ServerException $e) {
            $res = [
                'message' => $e->getResponse()->getReasonPhrase(),
                'code' => $e->getResponse()->getStatusCode()
            ];
        }

        return $res;
    }
}
<?php

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Exception\ConnectException;

use aCommerce\CMBridgeSquid\SalesOrderStatus;

class SalesOrderStatusTest extends PHPUnit_Framework_TestCase
{
    private $baseUrl;
    private $tokenId;
    private $channelId;
    private $partnerId;


    public function setUp()
    {
        $this->tokenId = 'c34988bc25d14c90b5420166510e4ddb';
        $this->baseUrl =  'https://fulfillment.api.acommercedev.com/';
        $this->channelId = 'frisianflag';
        $this->partnerId = '143';
    }

    public function testGetByChannelId()
    {
        $url = $this->baseUrl . "channel/" . $this->channelId ."/sales-order-status";
        $salesOrderStatus = new SalesOrderStatus();

        $this->mockSalesOrderStatus($salesOrderStatus, [
            new Response(200, [], '[{"message": "success", "code": 200, "body": []}]'),
            new Response(200, [], '[{"message": "success", "code": 200, "body": []}]'),
            new Response(200, [], '[{"message": "success", "code": 200, "body": []}]')
        ]);
        $res = $salesOrderStatus->get($this->tokenId, $url);
        $this->assertNotEmpty($res['body']);
        $this->assertEquals("200", $res['code']);

        $url = $this->baseUrl . "channel/" . $this->channelId ."/sales-order-status?since=2015-12-30T06:00:00Z&orderStatus=NEW";
        $res = $salesOrderStatus->get($this->tokenId, $url);
        $this->assertNotEmpty($res['body']);
        $this->assertEquals("200", $res['code']);

        $url = $this->baseUrl . "channel/" . $this->channelId ."/sales-order-status?id=FRISIAN2015123100001";
        $res = $salesOrderStatus->get($this->tokenId, $url);
        $this->assertNotEmpty($res['body']);
        $this->assertEquals("200", $res['code']);
    }

    public function testGetByPartnerId(){
        $url = $this->baseUrl . "partner/" . $this->channelId ."/sales-order-status";
        $salesOrderStatus = new SalesOrderStatus();

        $this->mockSalesOrderStatus($salesOrderStatus, [
            new Response(200, [], '[{"message": "success", "code": 200, "body": []}]'),
            new Response(200, [], '[{"message": "success", "code": 200, "body": []}]'),
            new Response(200, [], '[{"message": "success", "code": 200, "body": []}]')
        ]);
        $res = $salesOrderStatus->get($this->tokenId, $url);
        $this->assertNotEmpty($res['body']);
        $this->assertEquals("200", $res['code']);

        $url = $this->baseUrl . "partner/" . $this->channelId ."/sales-order-status?since=2015-12-30T06:00:00Z&orderStatus=NEW";
        $res = $salesOrderStatus->get($this->tokenId, $url);
        $this->assertNotEmpty($res['body']);
        $this->assertEquals("200", $res['code']);

        $url = $this->baseUrl . "partner/" . $this->channelId ."/sales-order-status?id=FRISIAN2015123100001";
        $res = $salesOrderStatus->get($this->tokenId, $url);
        $this->assertNotEmpty($res['body']);
        $this->assertEquals("200", $res['code']);
    }

    private function mockSalesOrderStatus(SalesOrderStatus $salesOrderStatus, array $queue)
    {
        $mock = new MockHandler($queue);
        $handler = HandlerStack::create($mock);
        $salesOrderStatus->client = new Client(['handler'=>$handler]);
    }

}

<?php

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\HandlerStack;

use ChannelBridge\Cpms\ShippingOrderStatus;

class ShippingOrderStatusTest extends PHPUnit_Framework_TestCase
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

    public function testGetByPartnerId(){
        $url = $this->baseUrl . "partner/" . $this->partnerId ."/shipping-order-status";
        $shippingOrderStatus = new ShippingOrderStatus();

        $this->mockShippingOrderStatus($shippingOrderStatus, [
            new Response(200, [], '[{"message": "success", "code": 200, "body": []}]'),
            new Response(200, [], '[{"message": "success", "code": 200, "body": []}]'),
            new Response(200, [], '[{"message": "success", "code": 200, "body": []}]')
        ]);
        $res = $shippingOrderStatus->get($this->tokenId, $url);
        $this->assertNotEmpty($res['body']);
        $this->assertEquals("200", $res['code']);

        $url = $this->baseUrl . "partner/" . $this->partnerId ."/shipping-order-status?since=2015-12-30T06:00:00Z&shipOrderStatus=NEW";
        $res = $shippingOrderStatus->get($this->tokenId, $url);
        $this->assertNotEmpty($res['body']);
        $this->assertEquals("200", $res['code']);

        $url = $this->baseUrl . "partner/" . $this->partnerId ."/shipping-order-status/id?id=FRISIAN2015123100001";
        $res = $shippingOrderStatus->get($this->tokenId, $url);
        $this->assertNotEmpty($res['body']);
        $this->assertEquals("200", $res['code']);
    }

    private function mockShippingOrderStatus(ShippingOrderStatus $shippingOrderStatus, array $queue)
    {
        $mock = new MockHandler($queue);
        $handler = HandlerStack::create($mock);
        $shippingOrderStatus->client = new Client(['handler'=>$handler]);
    }

}

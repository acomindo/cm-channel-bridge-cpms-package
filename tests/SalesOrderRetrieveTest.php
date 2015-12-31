<?php

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Exception\ConnectException;

use aCommerce\CMBridgeSquid\SalesOrder;

class SalesOrderRetrieveTest extends PHPUnit_Framework_TestCase
{
    private $url;
    private $tokenId;

    public function setUp()
    {
        $this->tokenId = 'c641b964c62f4adaacf5b7c83fe4164b';
        $this->url =  'https://fulfillment.api.acommercedev.com/channel/frisianflag/order/';
    }

    public function testRetrieveSalesOrderUnauthorized()
    {
        $orderId = "FRISIAN20151231000012";
        $token = "xxxxxx";
        $salesOrderRetrieve = new SalesOrder();

        $this->mockSalesOrderRetrieve($salesOrderRetrieve, [
            new Response(401)
        ]);

        $res = $salesOrderRetrieve->retrieve($this->url,$token, $orderId);
        $this->assertEquals(401, $res['code']);
    }

    public function testRetrieveSalesOrderNotFound()
    {
        $orderId = "FRISIAN20151231000012";
        $token = "8cfab4544e5440e695ca575ca3e75b12";
        $salesOrderRetrieve = new SalesOrder();

        $this->mockSalesOrderRetrieve($salesOrderRetrieve, [
            new Response(404)
        ]);

        $res = $salesOrderRetrieve->retrieve($this->url,$token, $orderId);
        $this->assertEquals(404, $res['code']);
    }

    public function testRetrieveSalesOrderFound()
    {
        $orderId = "FRISIAN2015123100001";
        $token = "8cfab4544e5440e695ca575ca3e75b12";
        $salesOrderRetrieve = new SalesOrder();

        $json = "
            {\"customerInfo\":{\"addressee\":\"Dan Happiness\",\"address1\":\"964 Rama 4 Road\",\"address2\":\"\",\"subDistrict\":\"\",\"district\":\"\",\"city\":\"\",\"province\":\"Bangkok\",\"postalCode\":\"9999\",\"country\":\"Indonesia\",\"phone\":\"081347344234\",\"email\":\"capme001@gmail.com\"},\"orderShipmentInfo\":{\"addressee\":\"smith\",\"address1\":\"ciputat\",\"address2\":\"\",\"subDistrict\":\"ciputat\",\"district\":\"tangerang\",\"city\":\"\",\"province\":\"banten\",\"postalCode\":\"15412\",\"country\":\"Indonesia\",\"phone\":\"0812889977\",\"email\":\"capme001@gmail.com\"},\"orderItems\":[{\"partnerId\":\"143\",\"itemId\":\"FRSIANPRODUCT000001\",\"subTotal\":6000.0,\"qty\":1}],\"grossTotal\":12000,\"paymentType\":\"COD\",\"shippingType\":\"STANDARD_2_4_DAYS\",\"orderCreatedTime\":\"2015-12-31T09:28:00Z\",\"currUnit\":\"THB\"}
        ";
        $this->mockSalesOrderRetrieve($salesOrderRetrieve, [
            new Response(200, [], $json)
        ]);

        $res = $salesOrderRetrieve->retrieve($this->url,$token, $orderId);
        $this->assertEquals(200, $res['code']);
        $this->assertArrayHasKey("data", $res);
        $this->assertArrayHasKey("customerInfo", $res['data']);
        $this->assertArrayHasKey("orderShipmentInfo", $res['data']);
        $this->assertArrayHasKey("orderItems", $res['data']);
    }

    private function mockSalesOrderRetrieve(SalesOrder $salesOrderRetrieve, $queue)
    {
        $mock = new MockHandler($queue);
        $handler = HandlerStack::create($mock);
        $salesOrderRetrieve->client = new Client(['handler'=>$handler]);
    }


}

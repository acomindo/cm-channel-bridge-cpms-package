<?php

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Exception\ConnectException;

use ChannelBridge\Cpms\SalesOrder;

class SalesOrderTest extends PHPUnit_Framework_TestCase
{
    protected $url;
    protected $order;
    protected $tokenId;

    public function setUp()
    {
        $orderId = 'FRIS'. rand(1000, 9999);
        $this->url = "https://fulfillment.api.acommercedev.com/channel/frisianflag/order/{$orderId}";
        $this->tokenId = 'e0e66966ee1a49bcafe7866365b27706';

        $order = '
        {
            "orderCreatedTime": "2015-06-18T10:30:40Z",
            "customerInfo": {
                "addressee": "Dan Happiness",
                "address1": "964 Rama 4 Road",
                "province": "Bangkok",
                "postalCode": "10500",
                "country": "Thailand",
                "phone": "081-000-0000",
                "email": "smith@a.com"
            },
            "orderShipmentInfo": {
                "addressee": "Smith Happiness",
                "address1": "111 Rama 4 rd.",
                "address2": "",
                "subDistrict": "Silom",
                "district": "Bangrak",
                "city": "",
                "province": "Bangkok",
                "postalCode": "10500",
                "country": "Thailand",
                "phone": "081-111-2222",
                "email": "smith@a.com"
            },
            "paymentType": "COD",
            "shippingType": "STANDARD_2_4_DAYS",
            "grossTotal": 12800,
            "currUnit": "THB",
            "orderItems": [{
                "partnerId": "143",
                "itemId": "FRSIAN64254110000000M",
                "qty": 2,
                "subTotal": 6000
            }]
        }
        ';
        $this->order = json_decode($order, true);
    }

    public function testOrderCreated()
    {
        $salesOrder = new SalesOrder();

        $this->mockSalesOrder($salesOrder, [
            new Response(201)
        ]);

        $res = $salesOrder->create($this->url, $this->tokenId, $this->order);
        $this->assertEquals(201, $res['code']);
    }

    public function testOrderCreatedValidationFailed()
    {
        $salesOrder = new SalesOrder();
        $this->order['customerInfo']['email'] = '';

        $this->mockSalesOrder($salesOrder, [
            new Response(422)
        ]);

        $res = $salesOrder->create($this->url, $this->tokenId, $this->order);
        $this->assertEquals(422, $res['code']);

    }

    public function testOrderCreatedModificationNotSupport() {
        $salesOrder = new SalesOrder();

        $this->mockSalesOrder($salesOrder, [
            new Response(501)
        ]);

        $res = $salesOrder->create($this->url, $this->tokenId, $this->order);
        $this->assertEquals(501, $res['code']);
    }

    public function testOrderCreatedNotFoundOrderItems()
    {
        $this->url = "http://fulfillment.api.acommercedev.com/channel/frisianflag/order/FRIS00014";
        $this->order['orderItems'][0]['partnerId'] = '20312';

        $salesOrder = new SalesOrder();

        $this->mockSalesOrder($salesOrder, [
            new Response(404)
        ]);

        $res = $salesOrder->create($this->url, $this->tokenId, $this->order);
        $this->assertEquals(404, $res['code']);
    }

    public function testSalesOrderConnectionError()
    {
        $salesOrder = new SalesOrder();

        $handlerContext = [
            'errno' => 28,
            'error' => 'Connection timed out after 5004 milliseconds',
            'http_code' => 0
        ];

        $this->mockSalesOrder($salesOrder, [
            new ConnectException('Network Error', new Request('POST', $this->url), null, $handlerContext)
        ]);

        $res = $salesOrder->create($this->url, $this->tokenId, $this->order);
        $this->assertEquals(0, $res['code']);
    }

    public function testGetNotFound()
    {
        $orderId = "FRISIAN20151231000012";
        $token = "8cfab4544e5440e695ca575ca3e75b12";
        $this->url =  'https://fulfillment.api.acommercedev.com/channel/frisianflag/order/'.$orderId;

        $salesOrder = new SalesOrder();

        $this->mockSalesOrder($salesOrder, [
            new Response(404)
        ]);

        $res = $salesOrder->get($token, $this->url.$orderId);
        $this->assertEquals(404, $res['code']);
    }

    public function testGetSuccess()
    {
        $orderId = "FRISIAN2015123100001";
        $token = "8cfab4544e5440e695ca575ca3e75b12";
        $this->url =  'https://fulfillment.api.acommercedev.com/channel/frisianflag/order/'.$orderId;
        $json = '
            {
                "customerInfo": {
                    "addressee": "Dan Happiness",
                    "address1": "964 Rama 4 Road",
                    "address2": "",
                    "subDistrict": "",
                    "district": "",
                    "city": "",
                    "province": "Bangkok",
                    "postalCode": "9999",
                    "country": "Indonesia",
                    "phone": "081347344234",
                    "email": "capme001@gmail.com"
                },
                "orderShipmentInfo": {
                    "addressee": "smith",
                    "address1": "ciputat",
                    "address2": "",
                    "subDistrict": "ciputat",
                    "district": "tangerang",
                    "city": "",
                    "province": "banten",
                    "postalCode": "15412",
                    "country": "Indonesia",
                    "phone": "0812889977",
                    "email": "capme001@gmail.com"
                },
                "orderItems": [{
                    "partnerId": "143",
                    "itemId": "FRSIANPRODUCT000001",
                    "subTotal": 6000.0,
                    "qty": 1
                }],
                "grossTotal": 12000,
                "paymentType": "COD",
                "shippingType": "STANDARD_2_4_DAYS",
                "orderCreatedTime": "2015-12-31T09:28:00Z",
                "currUnit": "THB"
            }
        ';

        $salesOrder = new SalesOrder();

        $this->mockSalesOrder($salesOrder, [
            new Response(200, [], $json)
        ]);

        $res = $salesOrder->get($token, $this->url);

        $this->assertEquals(200, $res['code']);
        $this->assertArrayHasKey("body", $res);
        $this->assertArrayHasKey("customerInfo", $res['body']);
        $this->assertArrayHasKey("orderShipmentInfo", $res['body']);
        $this->assertArrayHasKey("orderItems", $res['body']);
    }

    private function mockSalesOrder(SalesOrder $salesOrder, array $queue)
    {
        $mock = new MockHandler($queue);
        $handler = HandlerStack::create($mock);
        $salesOrder->client = new Client(['handler'=>$handler]);
    }
}
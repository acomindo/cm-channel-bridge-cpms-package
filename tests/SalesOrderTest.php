<?php

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Exception\ConnectException;

use aCommerce\CMBridgeSquid\SalesOrder;

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

        $order = '{"orderCreatedTime":"2015-06-18T10:30:40Z","customerInfo":{"addressee":"Dan Happiness",'
            .'"address1":"964 Rama 4 Road","province":"Bangkok","postalCode":"10500","country":"Thailand",'
            .'"phone":"081-000-0000","email":"smith@a.com"},"orderShipmentInfo":{"addressee":"Smith Happiness",'
            .'"address1":"111 Rama 4 rd.","address2":"","subDistrict":"Silom","district":"Bangrak","city":"",'
            .'"province":"Bangkok","postalCode":"10500","country":"Thailand","phone":"081-111-2222",'
            .'"email":"smith@a.com"},"paymentType":"COD","shippingType":"STANDARD_2_4_DAYS",'
            .'"grossTotal":12800,"currUnit":"THB","orderItems":[{"partnerId":"143",'
            .'"itemId":"FRSIAN64254110000000M","qty":2,"subTotal":6000}]}';
        $this->order = json_decode($order, true);
    }

    public function testOrderCreated()
    {
        $salesOrder = new SalesOrder();

        $this->mockSalesOrder($salesOrder, [
            new Response(201)
        ]);

        $res = $this->getSalesOrderResponse($salesOrder);
        $this->assertEquals(201, $res['code']);

    }

    public function testInvalidTokenId()
    {
        $salesOrder = new SalesOrder();
        $this->tokenId = '12312313';

        $this->mockSalesOrder($salesOrder, [
            new Response(401)
        ]);

        $res = $this->getSalesOrderResponse($salesOrder);
        $this->assertEquals(401, $res['code']);
    }

    public function testOrderCreatedValidationFailed()
    {
        $salesOrder = new SalesOrder();
        $this->order['customerInfo']['email'] = '';

        $this->mockSalesOrder($salesOrder, [
            new Response(422)
        ]);

        $res = $this->getSalesOrderResponse($salesOrder);
        $this->assertEquals(422, $res['code']);

    }

    public function testOrderCreatedModificationNotSupport() {
        $salesOrder = new SalesOrder();

        $this->mockSalesOrder($salesOrder, [
            new Response(501)
        ]);

        $res = $this->getSalesOrderResponse($salesOrder);
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

        $res = $this->getSalesOrderResponse($salesOrder);
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

        $res = $this->getSalesOrderResponse($salesOrder);
        $this->assertEquals(0, $res['code']);
    }

    private function getSalesOrderResponse(SalesOrder $salesOrder)
    {
        return $salesOrder->create($this->url, $this->tokenId, $this->order);
    }

    private function mockSalesOrder(SalesOrder $salesOrder, array $queue)
    {
        $mock = new MockHandler($queue);
        $handler = HandlerStack::create($mock);
        $salesOrder->client = new Client(['handler'=>$handler]);
    }
}
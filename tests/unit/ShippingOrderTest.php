<?php

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Exception\ConnectException;

use ChannelBridge\Cpms\ShippingOrder;

class ShippingOrderTest extends PHPUnit_Framework_TestCase
{
    protected $url;
    protected $order;
    protected $tokenId;
    protected $urlPatch;
    protected $orderPatch;

    public function setUp()
    {
        $orderId = 'FRIS'. rand(1000, 9999);
        $this->url = "https://shipping.api.acommercedev.com/partner/143/order/{$orderId}";
        $this->tokenId = 'e0e66966ee1a49bcafe7866365b27706';

        $order = '
        {
            "shipCreatedTime":"2015-06-24T04:47:00Z",
            "shipSender":{
                "addressee":"Best Seller Company",
                "address1":"Dusit Thani Hotel",
                "address2":"964 Rama 4 Road",
                "subDistrict":"Silom",
                "district":"Bangrak",
                "city":"",
                "province":"Bangkok",
                "postalCode":"10100",
                "country":"Thailand",
                "phone":"0888888888",
                "email ":"one@a.com"
            },
            "shipShipment":{
                "addressee":"Smith Happiness",
                "address1":"111 Rama 4 rd.",
                "address2":"",
                "subDistrict":"Silom",
                "district":"Bangrak",
                "city":"",
                "province":"Bangkok",
                "postalCode":"10500",
                "country":"Thailand",
                "phone":"0111111111",
                "email":"two@a.com"
            },
            "shipPickup":{
                "addressee":"Wonderful Merchant",
                "address1":"Victory Monument",
                "address2":"",
                "subDistrict":"Thanon Phaya Thai",
                "district":"Ratchathewi",
                "city":"",
                "province":"Bangkok",
                "postalCode":"10400",
                "country":"Thailand",
                "phone":"022202200",
                "email":"order@wonderful-merchant.asia"
            },
            "shipInsurance":false,
            "shipCurrency":"THB",
            "shipShippingType":"NEXT_DAY",
            "shipPaymentType":"NON_COD",
            "shipPickingList":[
                {
                    "itemDescription":"A flagship smartphone",
                    "itemQuantity":1
                },
                {
                    "itemDescription":"Gadgets for a flagship smartphone",
                    "itemQuantity":4
                }
            ]
        }
        ';
        $this->order = json_decode($order, true);

        //patch is only for custom project
        $orderId = 'FRIS'. rand(1000, 9999);
        $this->urlPatch = "https://shipping.api.acommercedev.com/partner/143/order/{$orderId}/ship-packages";
        $orderPatch = '
        [
            {
                "packageWeight":1.5,
                "packageHeight":10,
                "packageWidth":30,
                "packageDepth":30,
                "packageDeclaredValue":2500.00,
                "packageItems":[
                    {
                        "itemDescription":"A widget that can be used for amazing things",
                        "itemQuantity":1
                    },
                    {
                        "itemDescription":"A much smaller widget that is not so useful",
                        "itemQuantity":10
                    }
                ]
            }
        ]
        ';
        $this->orderPatch = json_decode($orderPatch, true);
    }

    public function testShippingOrderCreated()
    {
        $shippingOrder = new ShippingOrder();

        $this->mockShippingOrder($shippingOrder, [
            new Response(201)
        ]);

        $res = $shippingOrder->create($this->url, $this->tokenId, $this->order);
        $this->assertEquals(201, $res['code']);
    }

    public function testShippingOrderCreatedValidationFailed()
    {
        $shippingOrder = new ShippingOrder();
        unset($this->order['shipSender']);

        $this->mockShippingOrder($shippingOrder, [
            new Response(422)
        ]);

        $res = $shippingOrder->create($this->url, $this->tokenId, $this->order);
        $this->assertEquals(422, $res['code']);
    }

    public function testOrderCreatedModificationNotSupport() {
        $shippingOrder = new ShippingOrder();

        $this->mockShippingOrder($shippingOrder, [
            new Response(501)
        ]);

        $res = $shippingOrder->create($this->url, $this->tokenId, $this->order);
        $this->assertEquals(501, $res['code']);
    }

    public function testShippingOrderPatched()
    {
        $shippingOrder = new ShippingOrder();

        $this->mockShippingOrder($shippingOrder, [
            new Response(201)
        ]);

        $res = $shippingOrder->update($this->urlPatch, $this->tokenId, $this->orderPatch);
        $this->assertEquals(201, $res['code']);
    }

    private function mockShippingOrder(ShippingOrder $shippingOrder, array $queue)
    {
        $mock = new MockHandler($queue);
        $handler = HandlerStack::create($mock);
        $shippingOrder->client = new Client(['handler'=>$handler]);
    }
}
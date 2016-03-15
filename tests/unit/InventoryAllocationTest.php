<?php

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\HandlerStack;

use ChannelBridge\Cpms\InventoryAllocation;

class InventoryAllocationTest extends PHPUnit_Framework_TestCase
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

    public function testGetInventoryAllocation()
    {
        $url = $this->baseUrl . "channel/" . $this->channelId . "/allocation/merchant/" . $this->partnerId . "?since=2010-01-01T00:00:01Z";
        $inventoryAllocation = new InventoryAllocation();

        $this->mockInventoryAllocation($inventoryAllocation,
            [
                new Response(
                    200,
                    [
                        'Link' => '<https://fulfillment.api.acommercedev.com/channel/frisianflag/allocation/merchant/143?page=2&page_size=2>; rel="next", <https://fulfillment.api.acommercedev.com/channel/frisianflag/allocation/merchant/143?page=last&page_size=2>; rel="last"'
                    ],
                    '
                        [{
                            "sku": "item0",
                            "updatedTime": "2015-08-27T14:15:24.284Z",
                            "qty": "10"
                        },
                        {
                            "sku": "item01",
                            "updatedTime": "2015-08-27T13:40:16.695Z",
                            "qty": "0"
                        },
                        {
                            "sku": "item02",
                            "updatedTime": "2015-08-27T13:40:16.695Z",
                            "qty": "0"
                        },
                        {
                            "sku": "item03",
                            "updatedTime": "2015-08-27T13:40:16.695Z",
                            "qty": "UNLIMITED"
                        },
                        {
                            "sku": "item10",
                            "updatedTime": "2015-08-27T14:15:24.284Z",
                            "qty": "0"
                        },
                        {
                            "sku": "item100",
                            "updatedTime": "2015-08-27T14:15:24.284Z",
                            "qty": "100"
                        },
                        {
                            "sku": "item101",
                            "updatedTime": "2015-08-27T14:15:24.284Z",
                            "qty": "101"
                        },
                        {
                            "sku": "item102",
                            "updatedTime": "2015-08-27T14:15:24.284Z",
                            "qty": "102"
                        },
                        {
                            "sku": "item103",
                            "updatedTime": "2015-08-27T14:15:24.284Z",
                            "qty": "103"
                        },
                        {
                            "sku": "item104",
                            "updatedTime": "2015-08-27T14:15:24.284Z",
                            "qty": "104"
                        }]
                    '
                )
            ]
        );

        $res = $inventoryAllocation->get($this->tokenId, $url);

        $this->assertEquals("200", $res['code']);
        $this->assertNotEmpty($res['body']);
        $this->assertEquals("https://fulfillment.api.acommercedev.com/channel/frisianflag/allocation/merchant/143?page=2&page_size=2",
            $res['next']);
    }

    private function mockInventoryAllocation(InventoryAllocation $inventoryAllocation, array $queue)
    {
        $mock = new MockHandler($queue);
        $handler = HandlerStack::create($mock);
        $inventoryAllocation->client = new Client(['handler'=>$handler]);
    }

}

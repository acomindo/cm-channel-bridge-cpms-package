<?php

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Exception\ConnectException;

use Acp\Cmp\SalesOrderStatus;

class SalesOrderStatusTest extends PHPUnit_Framework_TestCase
{
    private $baseUrl;
    private $tokenId;

    public function setUp()
    {
        $this->tokenId = 'c641b964c62f4adaacf5b7c83fe4164b';
        $this->baseUrl =  'https://fulfillment.api.acommercedev.com/';
    }

    public function testGetSalesOrderStatusPartnerId()
    {
        $salesOrderStatus = new SalesOrderStatus($this->baseUrl);

        $this->mockSalesOrderStatus($salesOrderStatus, [
            new Response(200, [], '[{"orderId": "FRIS00201"}]'),
            new Response(200, [], '{}')
        ]);

        $res = $salesOrderStatus->getPartner($this->tokenId, 143);
        $this->assertNotEmpty($res['body']);

        $res = $salesOrderStatus->getPartner($this->tokenId, 1423);
        $this->assertEmpty($res['body']);
    }

    public function testGetSalesOrderStatusChannelId() {
        $salesOrderStatus = new SalesOrderStatus($this->baseUrl);

        $this->mockSalesOrderStatus($salesOrderStatus, [
            new Response(200, [], '[{"orderId": "FRIS00201"}]'),
            new Response(200, [], '{}')
        ]);

        $res = $salesOrderStatus->getChannel($this->tokenId, 'frisianflag');
        $this->assertNotEmpty($res['body']);

        $res = $salesOrderStatus->getChannel($this->tokenId, 'frisianflag123');
        $this->assertEmpty($res['body']);
    }

    public function testGetSalesOrderStatusPartnerIdWithOrderId()
    {
        $salesOrderStatus = new SalesOrderStatus($this->baseUrl, null, null, ['FRIS00011']);

        $this->mockSalesOrderStatus($salesOrderStatus, [
            new Response(200, [], '[{"orderId": "FRIS00201"}]')
        ]);

        $res = $salesOrderStatus->getPartner($this->tokenId, 143);
        $this->assertNotEmpty($res['body']);
    }

    public function testGetSalesOrderStatusChannelIdWithOrderId()
    {
        $salesOrderStatus = new SalesOrderStatus($this->baseUrl, null, null, ['FRIS00011']);

        $this->mockSalesOrderStatus($salesOrderStatus, [
            new Response(200, [], '[{"orderId": "FRIS00201"}]'),
        ]);

        $res = $salesOrderStatus->getChannel($this->tokenId, 'frisianflag');
        $this->assertNotEmpty($res['body']);
    }

    public function testGetSalesOrderStatusPartnerIdWithTime()
    {
        $salesOrderStatus = new SalesOrderStatus($this->baseUrl, '2015-12-20T04:36:17Z');

        $this->mockSalesOrderStatus($salesOrderStatus, [
            new Response(200, [], '[{"orderId": "FRIS00201"}]'),
            new Response(200, [], '{}')
        ]);

        $res = $salesOrderStatus->getPartner($this->tokenId, 143);
        $this->assertNotEmpty($res['body']);

        $salesOrderStatus = new SalesOrderStatus($this->baseUrl, '3016-12-20T04:36:17Z');
        $res = $salesOrderStatus->getPartner($this->tokenId, 143);
        $this->assertEmpty($res['body']);
    }

    public function testGetSalesOrderStatusChannelIdWithTime()
    {
        $salesOrderStatus = new SalesOrderStatus($this->baseUrl, '2015-12-20T04:36:17Z');

        $this->mockSalesOrderStatus($salesOrderStatus, [
            new Response(200, [], '[{"orderId": "FRIS00201"}]'),
            new Response(200, [], '{}')
        ]);

        $res = $salesOrderStatus->getChannel($this->tokenId, 'frisianflag');
        $this->assertNotEmpty($res['body']);

        $salesOrderStatus = new SalesOrderStatus($this->baseUrl, '3016-12-20T04:36:17Z');
        $res = $salesOrderStatus->getChannel($this->tokenId, 'frisianflag');
        $this->assertEmpty($res['body']);
    }

    public function testGetSalesOrderStatusPartnerIdWithOrderStatus()
    {
        $salesOrderStatus = new SalesOrderStatus($this->baseUrl, null, 'ERROR');

        $this->mockSalesOrderStatus($salesOrderStatus, [
            new Response(200, [], '[{"orderId": "FRIS00201"}]'),
            new Response(200, [], '{}')
        ]);

        $res = $salesOrderStatus->getPartner($this->tokenId, 143);
        $this->assertNotEmpty($res['body']);

        $salesOrderStatus = new SalesOrderStatus($this->baseUrl, '3016-12-20T04:36:17Z');
        $res = $salesOrderStatus->getPartner($this->tokenId, 143);
        $this->assertEmpty($res['body']);
    }

    public function testGetSalesOrderStatusChannelIdWithOrderStatus()
    {
        $salesOrderStatus = new SalesOrderStatus($this->baseUrl, null, 'ERROR');

        $this->mockSalesOrderStatus($salesOrderStatus, [
            new Response(200, [], '[{"orderId": "FRIS00201"}]'),
            new Response(200, [], '{}')
        ]);

        $res = $salesOrderStatus->getChannel($this->tokenId, 'frisianflag');
        $this->assertNotEmpty($res['body']);

        $salesOrderStatus = new SalesOrderStatus($this->baseUrl, '3016-12-20T04:36:17Z');
        $res = $salesOrderStatus->getChannel($this->tokenId, 'frisianflag');
        $this->assertEmpty($res['body']);
    }

    public function testClientProblem()
    {
        $salesOrderStatus = new SalesOrderStatus($this->baseUrl);

        $this->mockSalesOrderStatus($salesOrderStatus, [
            new Response(404)
        ]);

        $res = $salesOrderStatus->getChannel($this->tokenId, 'frisianflag');
        $this->assertEquals(404, $res['code']);
    }

    public function testServerProblem()
    {
        $salesOrderStatus = new SalesOrderStatus($this->baseUrl);

        $this->mockSalesOrderStatus($salesOrderStatus, [
            new Response(501)
        ]);

        $res = $salesOrderStatus->getChannel($this->tokenId, 'frisianflag');
        $this->assertEquals(501, $res['code']);
    }

    public function testSalesOrderStatusConnectionError()
    {
        $salesOrderStatus = new SalesOrderStatus($this->baseUrl, '2015-12-20T04:36:17Z');

        $handlerContext = [
            'errno' => 28,
            'error' => 'Connection timed out after 10004 milliseconds',
            'http_code' => 0
        ];

        $this->mockSalesOrderStatus($salesOrderStatus, [
            new ConnectException('Network Error', new Request('GET', $this->baseUrl), null, $handlerContext)
        ]);

        $res = $salesOrderStatus->getChannel($this->tokenId, 'frisianflag');
        $this->assertEquals(0, $res['code']);
    }

    private function mockSalesOrderStatus(SalesOrderStatus $salesOrderStatus, $queue)
    {
        $mock = new MockHandler($queue);
        $handler = HandlerStack::create($mock);
        $salesOrderStatus->client = new Client(['handler'=>$handler]);
    }


}

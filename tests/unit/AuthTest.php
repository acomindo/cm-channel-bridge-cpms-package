<?php

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Exception\ConnectException;


use ChannelBridge\Cpms\Auth;

class AuthTest extends PHPUnit_Framework_TestCase
{
    private $url;
    private $username;
    private $apiKey;

    public function setUp()
    {
        $this->url = 'https://api.acommercedev.com/identity/token';
        $this->username = 'frisianflag';
        $this->apiKey = 'frisianflag123!';
    }

    public function testAuthUnauthorize()
    {
        $this->username = "frisian";
        $this->apiKey = "frisianflag123!1123";
        $auth = new Auth();

        $this->mockAuth($auth, [
            new Response(401)
        ]);

        $res = $this->getAuthResponse($auth);
        $this->assertEquals(401, $res['code']);
    }


    public function testValidAuth()
    {
        $auth = new Auth();

        $mockRes =  new Response("200", [], '
                        {"username" : "frisianflag",
                        "token_id" : "942053802fd74990bb9721eff929f477",
                        "expires_at" : "2015-12-28T11:04:16.113003Z"}
                        '
                    );
        $this->mockAuth($auth, [$mockRes]);

        $res = $this->getAuthResponse($auth);
        $this->assertEquals(200, $res['code']);
        $this->assertEquals("942053802fd74990bb9721eff929f477", $res['body']['token_id']);

    }

    public function testConnectionProblem()
    {
        $auth = new Auth();

        $handlerContext = [
            'errno' => 28,
            'error' => 'Connection timed out after 5004 milliseconds',
            'http_code' => 0
        ];

        $this->mockAuth($auth, [
            new ConnectException('Network error', new Request('POST', $this->url), null, $handlerContext)
        ]);

        $res = $this->getAuthResponse($auth);
        $this->assertEquals(0, $res['code']);
    }

    public function testServerProblem()
    {
        $auth = new Auth();

        $this->mockAuth($auth, [
            new Response(501)
        ]);

        $res = $this->getAuthResponse($auth);
        $this->assertEquals(501, $res['code']);
    }

    private function getAuthResponse(Auth $auth)
    {
        return $auth->get($this->url, $this->username, $this->apiKey);
    }

    private function mockAuth(Auth $auth, array $queue)
    {
        $mock = new MockHandler($queue);
        $handler = HandlerStack::create($mock);
        $auth->client = new Client(['handler'=>$handler]);
    }
}

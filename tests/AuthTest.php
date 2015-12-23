<?php


use aCommerce\CMBridgeSquid\Auth;

class AuthTest extends PHPUnit_Framework_TestCase
{
    protected $url;

    public function setUp()
    {
        $this->url = "https://api.acommercedev.com/identity/token";
    }

    public function testInvalidAuth()
    {
        $username = "frisianflag";
        $apiKey = "frisianflag123!1123";
        $auth = new Auth($this->url);
        $res = $auth->getTokenId($this->url, $username, $apiKey);
        $this->assertEquals(401, $res['code']);
    }


    public function testValidAuth()
    {
        $username = "frisianflag";
        $apiKey = "frisianflag123!";

        $auth = new Auth();
        $res = $auth->getTokenId($this->url, $username, $apiKey);
        $this->assertEquals(200, $res['code']);
    }

    public function testTimeoutException()
    {
        $username = "frisianflag";
        $apiKey = "frisianflag123!";

        $auth = new Auth();
        $res = $auth->getTokenId("http://api.acommercedev.com:81/identity/token", $username, $apiKey);
        $this->assertEquals(0, $res['code']);
    }
}

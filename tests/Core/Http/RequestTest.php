<?php

namespace Ptf\Core\Http;

use \Ptf\Core\Http\Request;

class RequestTest extends \PHPUnit_Framework_TestCase
{
    private $testData = [
        'foo'   => 'bar',
        'hello' => 'world',
        'ns:test' => '1234',
        'ns:foo'  => 'baz'
    ];

    public function testGetRemoteAddr()
    {
        $request = new Request();
        $_SERVER['REMOTE_ADDR'] = '111.111.111.111';
        $this->assertSame('111.111.111.111', $request->getRemoteAddr());
        $_SERVER['HTTP_CLIENT_IP'] = '112.112.112.112';
        $this->assertSame('112.112.112.112', $request->getRemoteAddr());
        $_SERVER['HTTP_X_FORWARDED_FOR'] = '113.113.113.113, 114.114.114.114';
        $this->assertSame('113.113.113.113', $request->getRemoteAddr());
    }

    public function testGetProtocol()
    {
        $request = new Request();
        unset($_SERVER['HTTPS']);
        $this->assertSame('HTTP', $request->getProtocol());
        $_SERVER['HTTPS'] = '1';
        $this->assertSame('HTTPS', $request->getProtocol());
        $_SERVER['HTTPS'] = '0';
        $this->assertSame('HTTP', $request->getProtocol());
        $_SERVER['HTTPS'] = 'ON';
        $this->assertSame('HTTPS', $request->getProtocol());
    }

    public function testGetHost()
    {
        $request = new Request();
        $_SERVER['HTTP_HOST'] = 'www.example.com';
        $this->assertSame('www.example.com', $request->getHost());
    }

    public function testGetRequestUri()
    {
        $request = new Request();
        $_SERVER['REQUEST_URI'] = '/index.php?foo=bar';
        $this->assertSame('/index.php?foo=bar', $request->getRequestUri());
    }

    public function testGetRequestMethod()
    {
        $request = new Request();
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $this->assertSame('GET', $request->getRequestMethod());
    }

    public function testIsAjax()
    {
        $request = new Request();
        unset($_SERVER['HTTP_X_REQUESTED_WITH']);
        $this->assertFalse($request->isAjax());
        $_SERVER['HTTP_X_REQUESTED_WITH'] = 'XMLHttpRequest';
        $this->assertTrue($request->isAjax());
    }

    public function testGetGetVars()
    {
        $request = new Request();
        $testData = $this->testData;
        $_GET = $this->testData;
        $this->assertSame($testData, $request->getGetVars());
        $nsData = [
            'test' => '1234',
            'foo'  => 'baz'
        ];
        $this->assertSame($nsData, $request->getGetVars('ns'));
        $_GET = [];
    }

    public function testGetPostVars()
    {
        $request = new Request();
        $testData = $this->testData;
        $_POST = $this->testData;
        $this->assertSame($testData, $request->getPostVars());
        $nsData = [
            'test' => '1234',
            'foo'  => 'baz'
        ];
        $this->assertSame($nsData, $request->getPostVars('ns'));
        $_POST = [];
    }

    public function testGetCookieValues()
    {
        $request = new Request();
        $testData = $this->testData;
        $_COOKIE = $this->testData;
        $this->assertSame($testData, $request->getCookieValues());
        $nsData = [
            'test' => '1234',
            'foo'  => 'baz'
        ];
        $this->assertSame($nsData, $request->getCookieValues('ns'));
        $_COOKIE = [];
    }

    public function testGetRequestVars()
    {
        $request = new Request();
        $testData = $this->testData;
        $_REQUEST = $this->testData;
        $this->assertSame($testData, $request->getRequestVars());
        $nsData = [
            'test' => '1234',
            'foo'  => 'baz'
        ];
        $this->assertSame($nsData, $request->getRequestVars('ns'));
        $_REQUEST = [];
    }

    public function testGetGetVar()
    {
        $request = new Request();
        $_GET = $this->testData;
        $this->assertSame('bar', $request->getGetVar('foo'));
        $this->assertNull($request->getGetVar('bar'));
        $_GET = [];
        $this->assertNull($request->getGetVar('foo'));
    }

    public function testGetPostVar()
    {
        $request = new Request();
        $_POST = $this->testData;
        $this->assertSame('bar', $request->getPostVar('foo'));
        $this->assertNull($request->getPostVar('bar'));
        $_POST = [];
        $this->assertNull($request->getPostVar('foo'));
    }

    public function testGetCookieValue()
    {
        $request = new Request();
        $_COOKIE = $this->testData;
        $this->assertSame('bar', $request->getCookieValue('foo'));
        $this->assertNull($request->getCookieValue('bar'));
        $_COOKIE = [];
        $this->assertNull($request->getCookieValue('foo'));
    }

    public function testGetRequestVar()
    {
        $request = new Request();
        $_REQUEST= $this->testData;
        $this->assertSame('bar', $request->getRequestVar('foo'));
        $this->assertNull($request->getRequestVar('bar'));
        $_REQUEST = [];
        $this->assertNull($request->getRequestVar('foo'));
    }

    public function testGetAcceptLanguage()
    {
        $request = new Request();
        $_SERVER['HTTP_ACCEPT_LANGUAGE'] = 'de-de, de;q=0.8,en-us;q=0.5, fr-fr;q=0, en;q=0.3, *;q=0.6';
        $this->assertSame([
            'de-de' => '1',
            'de'    => '0.8',
            '*'     => '0.6',
            'en-us' => '0.5',
            'en'    => '0.3'
        ], $request->getAcceptLanguage());
        unset($_SERVER['HTTP_ACCEPT_LANGUAGE']);
        $this->assertSame([], $request->getAcceptLanguage());
    }

    public function testGetPreferredLanguage()
    {
        $request = new Request();
        $_SERVER['HTTP_ACCEPT_LANGUAGE'] = 'en-gb;q=0.7,fr-fr;q=1,en;q=0.3';
        $this->assertSame('fr-fr', $request->getPreferredLanguage());
        $this->assertSame('fr', $request->getPreferredLanguage(true));
        $_SERVER['HTTP_ACCEPT_LANGUAGE'] = 'fr-be;q=0.5, fr;q=0.6';
        $this->assertSame('fr', $request->getPreferredLanguage());
        $this->assertSame('fr', $request->getPreferredLanguage(true));
        unset($_SERVER['HTTP_ACCEPT_LANGUAGE']);
        $this->assertNull($request->getPreferredLanguage());
        $this->assertNull($request->getPreferredLanguage(true));
    }

}

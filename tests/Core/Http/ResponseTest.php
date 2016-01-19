<?php

namespace Ptf\Core\Http;

use \Ptf\Core\Http\Response;

class ResponseTest extends \PHPUnit_Framework_TestCase
{
    public function testSetHeader()
    {
        $response = new Response();
        $this->assertSame([], $response->getHeaders());
        $response
            ->setHeader('foo', 'bar')
            ->setHeader('baz');
        $this->assertSame(['foo' => 'bar', 'baz' => null], $response->getHeaders());
        $response->clearHeaders();
        $this->assertSame([], $response->getHeaders());
    }

    public function testSet404Header()
    {
        $response = new Response();
        $response->set404Header();
        $this->assertSame(['HTTP/1.0 404 Not Found' => null], $response->getHeaders());
    }

    public function testSetRedirectHeader()
    {
        $response = new Response();
        $response
            ->setRedirectHeader('http://www.example.com')
            ->setRedirectHeader('https://www.google.com', 301);
        $this->assertSame([
            'Location: http://www.example.com' => 302,
            'Location: https://www.google.com' => 301
        ], $response->getHeaders());
    }

    public function testSetContentTypeHeader()
    {
        $response = new Response();
        $response->setContentTypeHeader('text/html');
        $this->assertSame(['Content-type: text/html' => null], $response->getHeaders());
    }

    public function testSetJsonHeader()
    {
        $response = new Response();
        $response->setJsonHeader();
        $this->assertSame(['Content-type: application/json' => null], $response->getHeaders());
    }

    public function testSetNoCacheHeader()
    {
        $response = new Response();
        $response->setNoCacheHeader();
        $this->assertSame([
            'Cache-Control: no-cache, must-revalidate' => null,
            'Expires: Thu, 01 Jan 1970 00:00:00 GMT' => null
        ], $response->getHeaders());
    }

    public function testSetContent()
    {
        $response = new Response();
        $this->assertNull($response->getContent());
        $this->assertFalse($response->hasContent());
        $response->setContent('this is the content');
        $this->assertSame('this is the content', $response->getContent());
        $this->assertTrue($response->hasContent());
    }

    public function testSendHeaders()
    {
        $response = new Response();
        $response
            ->clearHeaders()
            ->setHeader('test')
            ->setJsonHeader()
            ->sendHeaders();
        if (function_exists('xdebug_get_headers')) {
            $this->assertSame(['test', 'Content-type: application/json'], xdebug_get_headers());
        } else {
            $this->assertSame(['test', 'Content-type: application/json'], headers_list());
        }
        $response->clearHeaders();
        if (function_exists('xdebug_get_headers')) {
            $this->assertSame([], xdebug_get_headers());
        } else {
            $this->assertSame([], headers_list());
        }
    }

    public function testSendContent()
    {
        $response = new Response();
        ob_start();
        $response
            ->setContent('testcontent')
            ->sendContent();
        $content = ob_get_clean();
        $this->assertSame('testcontent', $content);
    }

    public function testSend()
    {
        $response = new Response();
        ob_start();
        $response
            ->clearHeaders()
            ->setContent('some content')
            ->setHeader('some header')
            ->send();
        $content = ob_get_clean();
        $this->assertSame('some content', $content);
        if (function_exists('xdebug_get_headers')) {
            $this->assertSame(['some header'], xdebug_get_headers());
        } else {
            $this->assertSame(['some header'], headers_list());
        }
    }
}

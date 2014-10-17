<?php

namespace Ptf\Core;

use \Ptf\Core\Router;

class RouterTest extends \PHPUnit_Framework_TestCase
{
    public function testMatchRoute()
    {
        $context = \Ptf\Application::getContext();

        $this->assertTrue(Router::matchRoute('test/dummy', $context));
        $this->assertFalse(Router::matchRoute('Dummy', $context));
        $this->assertFalse(Router::matchRoute('/dummy', $context));
        $this->assertTrue(Router::matchRoute('test/index', $context));
        $this->assertTrue(Router::matchRoute('test/INDEX', $context));
        $this->assertTrue(Router::matchRoute('Test', $context));
        $this->assertTrue(Router::matchRoute('TEST/', $context));
        $this->assertFalse(Router::matchRoute('index', $context));
        $this->assertTrue(Router::matchRoute('/Index', $context));
        $this->assertTrue(Router::matchRoute('', $context));

        $this->assertFalse(Router::matchRoute('foo', $context));
        $this->assertTrue(Router::matchRoute('bar', $context));
        $this->assertTrue(Router::matchRoute('foo/bar', $context));
        $this->assertFalse(Router::matchRoute('baz', $context));
    }

    public function testMatchRequestRoute()
    {
        $context = \Ptf\Application::getContext();

        // /index => base/index
        $_REQUEST['action'] = 'index';
        $this->assertTrue(Router::matchRequestRoute($context));

        // /Dummy => base/dummy
        $_REQUEST['action'] = 'dummy';
        $this->assertFalse(Router::matchRequestRoute($context));

        // Test/Dummy => test/dummy
        $_REQUEST['controller'] = 'Test';
        $this->assertTrue(Router::matchRequestRoute($context));

        // BasE/Dummy => base/dummy
        $_REQUEST['controller'] = 'BasE';
        $this->assertFalse(Router::matchRequestRoute($context));

        // BasE/foo => base/foo
        $_REQUEST['action'] = 'foo';
        $this->assertFalse(Router::matchRequestRoute($context));

        // BasE/INDEX => base/index
        $_REQUEST['action'] = 'INDEX';
        $this->assertTrue(Router::matchRequestRoute($context));

        // BasE/ => base/index
        unset($_REQUEST['action']);
        $this->assertTrue(Router::matchRequestRoute($context));

        // / => base/index
        unset($_REQUEST['controller']);
        $this->assertTrue(Router::matchRequestRoute($context));
    }

}

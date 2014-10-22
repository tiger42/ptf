<?php

namespace Ptf\Core;

use \Ptf\Core\Router;

class RouterTest extends \PHPUnit_Framework_TestCase
{
    public function testMatchRoute()
    {
        $context = \Ptf\Application::getContext();

        $this->assertTrue(Router::matchRoute('test/dummy_action', $context));
        $this->assertTrue(Router::matchRoute('test/DummyAction', $context));
        $this->assertFalse(Router::matchRoute('DummyAction', $context));
        $this->assertFalse(Router::matchRoute('/dummy_action', $context));
        $this->assertTrue(Router::matchRoute('test/index', $context));
        $this->assertTrue(Router::matchRoute('test/INDEX', $context));
        $this->assertTrue(Router::matchRoute('Test', $context));
        $this->assertTrue(Router::matchRoute('TEST/', $context));
        $this->assertFalse(Router::matchRoute('index', $context));
        $this->assertTrue(Router::matchRoute('/Index', $context));
        $this->assertTrue(Router::matchRoute('', $context));

        $this->assertFalse(Router::matchRoute('foo', $context));
        $this->assertTrue(Router::matchRoute('bar', $context));   // => test/DummyAction
        $this->assertTrue(Router::matchRoute('foo/bar', $context));   // => /index
        $this->assertFalse(Router::matchRoute('baz', $context));    // => index
    }

    public function testMatchRequestRoute()
    {
        $context = \Ptf\Application::getContext();

        // /index => Base/Index
        $_REQUEST['action'] = 'index';
        $this->assertTrue(Router::matchRequestRoute($context));

        // /Dummy_action => Base/DummyAction
        $_REQUEST['action'] = 'Dummy_action';
        $this->assertFalse(Router::matchRequestRoute($context));

        // Test/Dummy_action => Test/DummyAction
        $_REQUEST['controller'] = 'Test';
        $this->assertTrue(Router::matchRequestRoute($context));

        // BasE/Dummy_action => Base/DummyAction
        $_REQUEST['controller'] = 'BasE';
        $this->assertFalse(Router::matchRequestRoute($context));

        // BasE/foo => Base/Foo
        $_REQUEST['action'] = 'foo';
        $this->assertFalse(Router::matchRequestRoute($context));

        // BasE/INDEX => Base/Index
        $_REQUEST['action'] = 'INDEX';
        $this->assertTrue(Router::matchRequestRoute($context));

        // BasE/ => Base/Index
        unset($_REQUEST['action']);
        $this->assertTrue(Router::matchRequestRoute($context));

        // / => Base/Index
        unset($_REQUEST['controller']);
        $this->assertTrue(Router::matchRequestRoute($context));
    }

}

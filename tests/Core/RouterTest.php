<?php

namespace Ptf\Core;

class RouterTest extends \PHPUnit\Framework\TestCase
{
    public function testMatchRoute()
    {
        $context = \Ptf\Application::getContext();

        // FIXME: This is dirty!
        $context->controllerType = 'Base';

        ob_start();
        $this->assertInstanceOf('Ptf\\Controller\\Base', Router::matchRoute('base_test/dummy_action', $context));
        $this->assertInstanceOf('Ptf\\Controller\\Base', Router::matchRoute('BaseTest/DummyAction', $context));
        $this->assertFalse(Router::matchRoute('DummyAction', $context));
        $this->assertFalse(Router::matchRoute('/dummy_action', $context));
        $this->assertInstanceOf('Ptf\\Controller\\Base', Router::matchRoute('baseTest/index', $context));
        $this->assertInstanceOf('Ptf\\Controller\\Base', Router::matchRoute('basetest/INDEX', $context));
        $this->assertInstanceOf('Ptf\\Controller\\Base', Router::matchRoute('BaseTest', $context));
        $this->assertInstanceOf('Ptf\\Controller\\Base', Router::matchRoute('BASETEST/', $context));
        $this->assertFalse(Router::matchRoute('index', $context));
        $this->assertInstanceOf('Ptf\\Controller\\Base', Router::matchRoute('/Index', $context));
        $this->assertInstanceOf('Ptf\\Controller\\Base', Router::matchRoute('', $context));

        $this->assertFalse(Router::matchRoute('foo', $context));
        $this->assertInstanceOf('Ptf\\Controller\\Base', Router::matchRoute('bar', $context));   // => BaseTest/DummyAction
        $this->assertInstanceOf('Ptf\\Controller\\Base', Router::matchRoute('foo/bar', $context));   // => /index
        $this->assertFalse(Router::matchRoute('baz', $context));    // => index
        ob_get_clean();

        unset($context->controllerType);
    }

    public function testMatchRequestRoute()
    {
        $context = \Ptf\Application::getContext();
        // FIXME: This is dirty!
        $context->controllerType = 'Base';

        ob_start();

        // /index => Base/Index
        $_REQUEST['action'] = 'index';
        $this->assertInstanceOf('Ptf\\Controller\\Base', Router::matchRequestRoute($context));

        // /Dummy_action => Base/DummyAction
        $_REQUEST['action'] = 'Dummy_action';
        $this->assertFalse(Router::matchRequestRoute($context));

        // basetest/Dummy_action => BaseTest/DummyAction
        $_REQUEST['controller'] = 'basetest';
        $this->assertInstanceOf('Ptf\\Controller\\Base', Router::matchRequestRoute($context));

        // BasE/Dummy_action => Base/DummyAction
        $_REQUEST['controller'] = 'BasE';
        $this->assertFalse(Router::matchRequestRoute($context));

        // BasE/foo => Base/Foo
        $_REQUEST['action'] = 'foo';
        $this->assertFalse(Router::matchRequestRoute($context));

        // BasE/INDEX => Base/Index
        $_REQUEST['action'] = 'INDEX';
        $this->assertInstanceOf('Ptf\\Controller\\Base', Router::matchRequestRoute($context));

        // BasE/ => Base/Index
        unset($_REQUEST['action']);
        $this->assertInstanceOf('Ptf\\Controller\\Base', Router::matchRequestRoute($context));

        // / => Base/Index
        unset($_REQUEST['controller']);
        $this->assertInstanceOf('Ptf\\Controller\\Base', Router::matchRequestRoute($context));

        ob_end_clean();
        unset($context->controllerType);
    }
}

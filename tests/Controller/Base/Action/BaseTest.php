<?php

namespace Ptf\Controller\Base\Action;

use \Ptf\Controller\Base as BaseController;

class BaseTest extends \PHPUnit\Framework\TestCase
{
    public function testGetName()
    {
        $context = \Ptf\Application::getContext();

        ob_start();
        $controller = new BaseController('Base', $context);
        $action = new \PtfTest\Controller\Base\Action\Index('FooBar', $controller);
        $this->assertSame('FooBar', $action->getName());
        ob_end_clean();
    }

    public function testForward()
    {
        $context = \Ptf\Application::getContext();

        ob_start();
        $controller = new BaseController('BaseTest', $context);
        $action = new \PtfTest\Controller\Base\Action\Index('asdf', $controller);
        $action->forward('DummyAction');
        $this->assertInstanceOf('PtfTest\\Controller\\BaseTest\\Action\\DummyAction', $controller->getAction());
        ob_end_clean();
    }
}

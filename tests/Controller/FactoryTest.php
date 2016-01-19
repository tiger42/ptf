<?php

namespace Ptf\Controller;

use \Ptf\Controller;

class FactoryTest extends \PHPUnit_Framework_TestCase
{
    public function testCreateController()
    {
        $context = \Ptf\Application::getContext();

        $controller = Factory::createController('', $context);
        $this->assertInstanceOf('\\Ptf\\Controller\\Base', $controller);
        $this->assertSame($controller, $context->getController());

        $controller = Factory::createController('foo', $context);
        $this->assertInstanceOf('\\Ptf\\Controller\\Base', $controller);
        $this->assertSame($controller, $context->getController());

        $controller = Factory::createController('my_controller', $context);
        $this->assertInstanceOf('\\Ptf\\Controller\\Base', $controller);
        $this->assertInstanceOf('\\PtfTest\\Controller\\MyController', $controller);
        $this->assertSame($controller, $context->getController());
    }

    public function testCreateControllerException()
    {
        $context = \Ptf\Application::getContext();

        $this->setExpectedException(
            'Exception',
            'Ptf\Controller\Factory::createController: Controller must extend base controller: PtfTest\\Controller\\Invalid');
        Factory::createController('invalid', $context);
    }
}

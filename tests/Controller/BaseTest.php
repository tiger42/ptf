<?php

namespace Ptf\Controller;

use \Ptf\Controller\Base as BaseController;

class BaseTest extends \PHPUnit\Framework\TestCase
{
    public function testCheckAction()
    {
        $context = \Ptf\Application::getContext();

        $controller = new MyBaseController('Base', $context);
        $action = new \PtfTest\Controller\Base\Action\Index('Index', $controller);
        $this->assertTrue($controller->checkAction($action));

        $action = new \PtfTest\Controller\Base\Action\WrongClass();
        $this->assertFalse($controller->checkAction($action));

        $this->assertFalse($controller->checkAction(null));
    }

    public function testExecuteAction()
    {
        $context = \Ptf\Application::getContext();

        $controller = new MyBaseController('Base', $context);
        $action = new \PtfTest\Controller\Base\Action\Index('Index', $controller);
        ob_start();
        $controller->executeAction($action);
        $returnValue = ob_get_clean();
        $this->assertSame('Base/Index', $returnValue);
    }

    public function testDispatch()
    {
        $context = \Ptf\Application::getContext();

        $controller = new BaseController('Base', $context);
        ob_start();
        $controller->dispatch();
        $returnValue = ob_get_clean();
        $this->assertSame('Base/Index', $returnValue);

        $controller = new BaseController('BaseTest', $context);
        ob_start();
        $controller->dispatch('DummyAction');
        $returnValue = ob_get_clean();
        $this->assertSame('BaseTest/DummyAction', $returnValue);
    }

    public function testDispatchException()
    {
        $context = \Ptf\Application::getContext();

        $controller = new BaseController('Invalid', $context);
        $this->expectException('\\Ptf\\Core\\Exception\\InvalidAction');
        $this->expectExceptionMessage(
            'Ptf\Controller\Base::dispatch: Action class not found: PtfTest\Controller\Invalid\Action\Index');
        $controller->dispatch();
    }

    public function testDispatchException2()
    {
        $context = \Ptf\Application::getContext();

        $controller = new BaseController('Base', $context);
        $this->expectException('\\Ptf\\Core\\Exception\\InvalidAction');
        $this->expectExceptionMessage(
            'Ptf\Controller\Base::dispatch: Action class not found: PtfTest\Controller\Base\Action\Invalid');
        $controller->dispatch('invalid');
    }

    public function testDispatchException3()
    {
        $context = \Ptf\Application::getContext();

        $controller = new BaseController('Base', $context);
        $this->expectException('\\Exception');
        $this->expectExceptionMessage(
            'Ptf\Controller\Base::dispatch: Action must extend base action: PtfTest\Controller\Base\Action\WrongClass');
        $controller->dispatch('WrongClass');
    }

    public function testGetName()
    {
        $context = \Ptf\Application::getContext();

        $controller = new BaseController('Foobar', $context);
        $this->assertSame('Foobar', $controller->getName());
    }

    public function testGetDefaultActionName()
    {
        $context = \Ptf\Application::getContext();

        $controller = new BaseController('Foobar', $context);
        $this->assertSame('Index', $controller->getDefaultActionName());
    }

    public function testGetAction()
    {
        $context = \Ptf\Application::getContext();

        $controller = new BaseController('Base', $context);
        $this->assertNull($controller->getAction());
        ob_start();
        $controller->dispatch();
        ob_get_clean();
        $this->assertInstanceOf('PtfTest\\Controller\\Base\\Action\\Index', $controller->getAction());

        $controller = new BaseController('BaseTest', $context);
        $this->assertNull($controller->getAction());
        ob_start();
        $controller->dispatch();
        ob_get_clean();
        $this->assertInstanceOf('PtfTest\\Controller\\BaseTest\\Action\\Index', $controller->getAction());
    }

    public function testForward()
    {
        $context = \Ptf\Application::getContext();

        // FIXME: This is dirty!
        $context->controllerType = 'Base';

        ob_start();
        $controller = new BaseController('Base', $context);
        $controller = $controller->forward('AnotherAction');
        $this->assertInstanceOf('PtfTest\\Controller\\Base\\Action\\AnotherAction', $controller->getAction());

        $controller = $controller->forward('BaseTest/Index');
        $this->assertInstanceOf('PtfTest\\Controller\\BaseTest\\Action\\Index', $controller->getAction());

        $controller = $controller->forward('BaseTest/DummyAction');
        $this->assertInstanceOf('PtfTest\\Controller\\BaseTest\\Action\\DummyAction', $controller->getAction());

        $controller = $controller->forward('basetest/index');
        $this->assertInstanceOf('PtfTest\\Controller\\BaseTest\\Action\\Index', $controller->getAction());
        ob_get_clean();

        unset($context->controllerType);
    }
}

class MyBaseController extends BaseController
{
    public function checkAction($action)
    {
        return parent::checkAction($action);
    }

    public function executeAction(\Ptf\Controller\Base\Action\Base $action)
    {
        return parent::executeAction($action);
    }
}

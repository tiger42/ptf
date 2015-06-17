<?php

namespace Ptf\Controller;

use \Ptf\Controller\Base as BaseController;

class BaseTest extends \PHPUnit_Framework_TestCase
{
    public function testDispatch()
    {
        $context = \Ptf\Application::getContext();

        $controller = new BaseController('Base', $context);
        $controller->dispatch();
        $this->assertSame('Base/Index', $context->getResponse()->getContent());

        $controller = new BaseController('Test', $context);
        $controller->dispatch('DummyAction');
        $this->assertSame('Test/DummyAction', $context->getResponse()->getContent());
    }

    public function testDispatchException()
    {
        $context = \Ptf\Application::getContext();

        $controller = new BaseController('Invalid', $context);
        $this->setExpectedException(
            '\\Ptf\\Core\\Exception\InvalidAction',
            'Ptf\Controller\Base::dispatch: Action class not found: PtfTest\Controller\Invalid\Action\Index');
        $controller->dispatch();
    }

    public function testDispatchException2()
    {
        $context = \Ptf\Application::getContext();

        $controller = new BaseController('Base', $context);
        $this->setExpectedException(
            '\\Ptf\\Core\\Exception\\InvalidAction',
            'Ptf\Controller\Base::dispatch: Action class not found: PtfTest\Controller\Base\Action\Invalid');
        $controller->dispatch('invalid');
    }

    public function testDispatchException3()
    {
        $context = \Ptf\Application::getContext();

        $controller = new BaseController('Base', $context);
        $this->setExpectedException(
            '\\Exception',
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

        $controller = new \PtfTest\Controller\MyController('MyController', $context);
        $this->assertSame('MyDefaultAction', $controller->getDefaultActionName());

    }

    public function testGetAction()
    {
        $context = \Ptf\Application::getContext();

        $controller = new BaseController('Base', $context);
        $this->assertNull($controller->getAction());
        $controller->dispatch();
        $this->assertInstanceOf('PtfTest\\Controller\\Base\\Action\\Index', $controller->getAction());

        $controller = new BaseController('Test', $context);
        $this->assertNull($controller->getAction());
        $controller->dispatch();
        $this->assertInstanceOf('PtfTest\\Controller\\Test\\Action\\Index', $controller->getAction());

        $controller = new \PtfTest\Controller\MyController('MyController', $context);
        $this->assertNull($controller->getAction());
        $controller->dispatch();
        $this->assertInstanceOf('PtfTest\\Controller\\MyController\\Action\\MyDefaultAction', $controller->getAction());

        $controller = new \PtfTest\Controller\MyController('MyController', $context);
        $this->assertNull($controller->getAction());
        $controller->dispatch('DummyAction');
        $this->assertInstanceOf('PtfTest\\Controller\\MyController\\Action\\DummyAction', $controller->getAction());
    }

    public function testForward()
    {
        $context = \Ptf\Application::getContext();

        $controller = new BaseController('MyController', $context);
        $controller->forward('DummyAction');
        $this->assertInstanceOf('PtfTest\\Controller\\MyController\\Action\\DummyAction', $controller->getAction());
        $controller->forward('MyController/MyDefaultAction');
        $this->assertInstanceOf('PtfTest\\Controller\\MyController\\Action\\MyDefaultAction', $controller->getAction());

        $controller->forward('Test/DummyAction');
        $this->assertSame('Test/DummyAction', $context->getResponse()->getContent());
    }

    public function testForward404()
    {
        $context = \Ptf\Application::getContext();

        $controller = new BaseController('Base', $context);
        ob_start();
        try {
            $controller->forward404();
        } catch (\Ptf\Core\Exception\SystemExit $e) {
        }
        $content = ob_get_clean();
        $this->assertRegExp('/<title>404 Not Found<\/title>.*<h1>Error 404<\/h1>/ms', $content);
        $headers = $context->getResponse()->getHeaders();
        $this->assertSame(['HTTP/1.0 404 Not Found' => null], $headers);
        $context->getResponse()->clearHeaders();
    }

    public function testRedirect()
    {
        $context = \Ptf\Application::getContext();

        $controller = new BaseController('Base', $context);
        try {
            $controller->redirect('http://www.example.com', 301);
        } catch (\Ptf\Core\Exception\SystemExit $e) {
        }
        $headers = $context->getResponse()->getHeaders();
        $this->assertSame(['Location: http://www.example.com' => 301], $headers);
        $context->getResponse()->clearHeaders();

        try {
            $controller->redirect('http://www.example.com/foo');
        } catch (\Ptf\Core\Exception\SystemExit $e) {
        }
        $headers = $context->getResponse()->getHeaders();
        $this->assertSame(['Location: http://www.example.com/foo' => 302], $headers);
        $context->getResponse()->clearHeaders();
    }

}

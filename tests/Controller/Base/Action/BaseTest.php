<?php

namespace Ptf\Controller\Base\Action;

use \Ptf\Controller\Base as BaseController;

class BaseTest extends \PHPUnit_Framework_TestCase
{
    public function testGetName()
    {
        $context = \Ptf\Application::getContext();

        $controller = new BaseController('Base', $context);
        $action = new \PtfTest\Controller\Base\Action\Index('FooBar', $controller);
        $this->assertSame('FooBar', $action->getName());
    }

    public function testForward()
    {
        $context = \Ptf\Application::getContext();

        $controller = new BaseController('Test', $context);
        $action = new \PtfTest\Controller\Base\Action\Index('Test', $controller);
        $action->forward('DummyAction');
        $this->assertInstanceOf('PtfTest\\Controller\\Test\\Action\\DummyAction', $controller->getAction());
    }

    public function testForward404()
    {
        $context = \Ptf\Application::getContext();

        $controller = new BaseController('Base', $context);
        $action = new \PtfTest\Controller\Base\Action\Index('Index', $controller);
        ob_start();
        try {
            $action->forward404();
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
        $action = new \PtfTest\Controller\Base\Action\Index('Index', $controller);
        try {
            $action->redirect('http://www.example.com', 301);
        } catch (\Ptf\Core\Exception\SystemExit $e) {
        }
        $headers = $context->getResponse()->getHeaders();
        $this->assertSame(['Location: http://www.example.com' => 301], $headers);
        $context->getResponse()->clearHeaders();

        try {
            $action->redirect('http://www.example.com/foo');
        } catch (\Ptf\Core\Exception\SystemExit $e) {
        }
        $headers = $context->getResponse()->getHeaders();
        $this->assertSame(['Location: http://www.example.com/foo' => 302], $headers);
        $context->getResponse()->clearHeaders();
    }

}

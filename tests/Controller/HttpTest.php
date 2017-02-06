<?php

namespace Ptf\Controller;

use \Ptf\Controller\Http as HttpController;

class HttpTest extends \PHPUnit\Framework\TestCase
{
    public function testCheckAction()
    {
        $context = \Ptf\Application::getContext();

        $controller = new MyHttpController('Base', $context);
        $action = new \PtfTest\Controller\Http\Action\Index('Index', $controller);
        $this->assertTrue($controller->checkAction($action));

        $action = new \PtfTest\Controller\Base\Action\Index('Index', $controller);
        $this->assertFalse($controller->checkAction($action));

        $this->assertFalse($controller->checkAction(null));
    }

    public function testExecuteAction()
    {
        $context = \Ptf\Application::getContext();

        $controller = new MyHttpController('Base', $context);
        $action = new \PtfTest\Controller\Http\Action\Index('Index', $controller);

        $_GET['HttpActionTest'] = 'ActionResponse';
        $controller->executeAction($action);
        $this->assertSame('ActionResponse', $context->getResponse()->getContent());
        $_GET = [];
    }

    public function testForward404()
    {
        $context = \Ptf\Application::getContext();

        $controller = new HttpController('Base', $context);
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

        $controller = new HttpController('Base', $context);
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

class MyHttpController extends HttpController
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

<?php

namespace Ptf\Controller\Http\Action;

use \Ptf\Controller\Http as HttpController;

class BaseTest extends \PHPUnit_Framework_TestCase
{
    public function testForward404()
    {
        $context = \Ptf\Application::getContext();

        $controller = new HttpController('Base', $context);
        $action = new \PtfTest\Controller\Http\Action\Index('Index', $controller);
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

        $controller = new HttpController('Base', $context);
        $action = new \PtfTest\Controller\Http\Action\Index('Index', $controller);
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

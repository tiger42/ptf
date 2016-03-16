<?php

namespace Ptf\View;

class BaseTest extends \PHPUnit_Framework_TestCase
{
    public function testMagicGettersAndSetters()
    {
        $view = $this->createView();
        $this->assertFalse(isset($view->foo));
        $this->assertNull($view->foo);
        $view->foo = 'bar';
        $this->assertTrue(isset($view->foo));
        $this->assertSame('bar', $view->foo);
        unset($view->foo);
        $this->assertFalse(isset($view->foo));
        $this->assertNull($view->foo);
        $this->assertSame(\Ptf\Application::getContext(), $view->context);
    }

    public function testAssign()
    {
        $view = $this->createView();
        $view->assign('test', 42);
        $view->assign(['test2' => 43, 'test3' => 44]);
        $this->assertSame(42, $view['test']);
        $this->assertSame(43, $view->test2);
        $this->assertSame([
            'context' => \Ptf\Application::getContext(),
            'test'  => 42, 'test2' => 43, 'test3' => 44
        ], $view->getAssignedVars());
    }

    public function testSetTemplateLanguage()
    {
        $view = $this->createView();
        $this->assertNull($view['tplLanguage']);
        $view->setTemplateLanguage('en');
        $this->assertSame('en', $view['tplLanguage']);
    }

    public function testClearCache()
    {
        $view = $this->createView();
        $this->assertNull($view->clearCache());
    }

    public function testIsCached()
    {
        $view = $this->createView();
        $this->assertFalse($view->isCached());
    }

    public function testFetch404Page()
    {
        $view = $this->createView();
        $this->assertRegExp('/<title>404 Not Found<\/title>.*<h1>Error 404<\/h1>/ms', $view->fetch404Page());
    }

    private function createView()
    {
        $config = new \Ptf\App\Config\View();
        $context = \Ptf\Application::getContext();

        return $this->getMockForAbstractClass('\Ptf\View\Base', [$config, $context]);
    }
}

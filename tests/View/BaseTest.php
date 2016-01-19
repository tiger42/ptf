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

        return $this->getMockForAbstractClass('\Ptf\View\Base', [$config]);
    }
}

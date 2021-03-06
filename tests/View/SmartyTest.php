<?php

namespace Ptf\View;

class SmartyTest extends \PHPUnit\Framework\TestCase
{
    public function testMagicGettersAndSetters()
    {
        $view = $this->createView();
        $smarty = $view->getSmartyObject();
        $this->assertFalse(isset($view->foo));
        $this->assertNull($view->foo);
        $view->foo = 'bar';
        $view['bar'] = 'foo';
        $this->assertTrue(isset($view->foo));
        $this->assertSame('bar', $view->foo);
        $this->assertSame('bar', $smarty->getTemplateVars('foo'));
        $this->assertSame('foo', $smarty->getTemplateVars('bar'));
        unset($view['foo']);
        $this->assertFalse(isset($view->foo));
        $this->assertNull($view->foo);
        $this->assertNull($smarty->getTemplateVars('foo'));
        $this->assertSame('foo', $smarty->getTemplateVars('bar'));
    }

    public function testAssign()
    {
        $view = $this->createView();
        $view->assign('test', 42);
        $view->assign(['test2' => 43, 'test3' => 44]);
        $this->assertSame(42, $view['test']);
        $this->assertSame(43, $view->test2);
        $this->assertSame(\Ptf\Application::getContext(), $view->context);
        $smarty = $view->getSmartyObject();
        $this->assertSame($view->getAssignedVars(), $smarty->getTemplateVars());
    }

    public function testRender()
    {
        $view = $this->createView();
        $view->render();
        $this->expectOutputString('foobarbaz');
    }

    public function testRender2()
    {
        $view = $this->createView();
        $view->test = 'hello';
        $view->render();
        $this->expectOutputString('foobarhellobaz');
    }

    public function testRender3()
    {
        $view = $this->createView();
        $view['test'] = 'world';
        $view->setCacheId('myCacheId');
        $view->render();
        $this->expectOutputString('foobarworldbaz');
    }

    public function testRenderException()
    {
        $view = new \Ptf\View\Smarty($this->createConfig(), \Ptf\Application::getContext());
        $this->expectException('\\RuntimeException');
        $this->expectExceptionMessage(
            'Ptf\View\Smarty::render: Smarty template has not been set');
        $view->render();
    }

    public function testFetch()
    {
        $view = $this->createView();
        $this->assertSame('foobarbaz', $view->fetch());
    }

    public function testFetch2()
    {
        $view = $this->createView();
        $view->test = 'hello';
        $this->assertSame('foobarhellobaz', $view->fetch());
    }

    public function testFetch3()
    {
        $view = $this->createView();
        $view['test'] = 'world';
        $view->setCacheId('CACHE');
        $this->assertSame('foobarworldbaz', $view->fetch());
    }

    public function testFetchException()
    {
        $view = new \Ptf\View\Smarty($this->createConfig(), \Ptf\Application::getContext());
        $this->expectException('\\RuntimeException');
        $this->expectExceptionMessage(
            'Ptf\View\Smarty::fetch: Smarty template has not been set');
        $view->fetch();
    }

    public function testFetch404Page()
    {
        $config = $this->createConfig();
        $config->template_404 = 'test_404.tpl';
        $view = new \Ptf\View\Smarty($config, \Ptf\Application::getContext());
        $this->assertSame('This is our own 404 template.', $view->fetch404Page());
    }

    public function testInclude()
    {
        $view = $this->createView('test_include.tpl');
        $this->assertSame('included:foobarbaasdfz:end', $view->fetch());
    }

    public function testGetSmartyObject()
    {
        $view = $this->createView();
        $smarty = $view->getSmartyObject();
        $this->assertInstanceOf('\\Smarty', $smarty);
    }

    public function testSetSmartyObject()
    {
        $view = $this->createView();
        $smarty = new \Smarty();
        $view->_setSmartyObject($smarty);
        $this->assertSame($smarty, $view->getSmartyObject());
    }

    public function testClearAll()
    {
        $config = $this->createConfig();
        $config->caching = 1;
        $view = new \Ptf\View\Smarty($config, \Ptf\Application::getContext());
        $view->setTemplateName('test.tpl');
        $directory = sys_get_temp_dir() . '/ptf_test';
        $files = array_diff(scandir($directory), ['..', '.']);
        $this->assertCount(0, $files);
        $view->fetch();
        $files = array_diff(scandir($directory), ['..', '.']);
        $this->assertCount(2, $files);
        $view->clearAll();
        $files = array_diff(scandir($directory), ['..', '.']);
        $this->assertCount(0, $files);
    }

    public function testIsCached()
    {
        $config = $this->createConfig();
        $view = new \Ptf\View\Smarty($config, \Ptf\Application::getContext());
        $view->setTemplateName('test.tpl');
        $view->fetch();
        $this->assertFalse($view->isCached());
        $this->assertFalse($view->isCached('test.tpl'));
    }

    public function testIsCached2()
    {
        $config = $this->createConfig();
        $config->caching = 1;
        $view = new \Ptf\View\Smarty($config, \Ptf\Application::getContext());
        $view->setTemplateName('test.tpl');
        $view->fetch();
        $this->assertTrue($view->isCached());
        $this->assertTrue($view->isCached('test.tpl'));
        $this->assertFalse($view->isCached('test.tpl', 'unknownId'));
        $view->clearCache();
        $this->assertFalse($view->isCached());
    }

    public function testIsCached3()
    {
        $config = $this->createConfig();
        $config->caching = 1;
        $view = new \Ptf\View\Smarty($config, \Ptf\Application::getContext());
        $view->setTemplateName('test.tpl');
        $view->fetch('testCacheId');
        $this->assertFalse($view->isCached());
        $this->assertFalse($view->isCached('invalid.tpl'));
        $this->assertFalse($view->isCached('invalid.tpl', 'testCacheId'));
        $this->assertTrue($view->isCached('test.tpl', 'testCacheId'));
        $view->clearCache();
        $this->assertFalse($view->isCached('test.tpl', 'testCacheId'));
    }

    public function testIsCached4()
    {
        $config = $this->createConfig();
        $config->caching = 1;
        $view = new \Ptf\View\Smarty($config, \Ptf\Application::getContext());
        $view->setTemplateName('test.tpl');
        $view->setCacheId('myCacheId');
        $view->fetch();
        $this->assertTrue($view->isCached());
        $this->assertFalse($view->isCached('invalid.tpl'));
        $this->assertFalse($view->isCached('invalid.tpl', 'myCacheId'));
        $this->assertTrue($view->isCached('test.tpl'));
        $this->assertFalse($view->isCached('test.tpl', 'otherCacheId'));
        $this->assertTrue($view->isCached('test.tpl', 'myCacheId'));
        $view->clearCache();
        $this->assertFalse($view->isCached('test.tpl'));
    }

    public function tearDown(): void
    {
        $view = $this->createView();
        $view->clearAll();
    }

    public static function tearDownAfterClass(): void
    {
        @rmdir(sys_get_temp_dir() . '/ptf_test');
    }

    private function createView($templateName = 'test.tpl')
    {
        $config = $this->createConfig();
        $view = new \Ptf\View\Smarty($config, \Ptf\Application::getContext());
        $view->setTemplateName($templateName);

        return $view;
    }

    private function createConfig()
    {
        $config = new \Ptf\App\Config\ViewSmarty();
        $config->template_dir = __DIR__ . '/templates/Smarty';
        $config->compile_check = 1;
        $config->compile_dir = $config->cache_dir = sys_get_temp_dir() . '/ptf_test';

        return $config;
    }

    private function renderView($view)
    {
        ob_start();
        $view->render();
        $output = ob_get_clean();

        return $output;
    }
}

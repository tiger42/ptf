<?php

namespace Ptf\View;

class PlainTest extends \PHPUnit_Framework_TestCase
{
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
        $view->render();
        $this->expectOutputString('foobarworldbaz');
    }

    public function testRenderException()
    {
        $view = new \Ptf\View\Plain($this->createConfig());
        $this->setExpectedException(
            '\\RuntimeException',
            'Ptf\View\Plain::render: PHP template has not been set');
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
        $this->assertSame('foobarworldbaz', $view->fetch());
    }

    public function testFetchException()
    {
        $view = new \Ptf\View\Plain($this->createConfig());
        $this->setExpectedException(
            '\\RuntimeException',
            'Ptf\View\Plain::fetch: PHP template has not been set');
        $view->fetch();
    }

    public function testFetch404Page()
    {
        $config = $this->createConfig();
        $config->template_404 = 'test_404.tpl';
        $view = new \Ptf\View\Plain($config);
        $this->assertSame('This is our own 404 template.', $view->fetch404Page());
    }

    public function testIncludeTpl()
    {
        $view = $this->createView('test_include.tpl');
        $this->assertSame('included:foobarbaasdfz:end', $view->fetch());
    }

    public function testPlugin()
    {
        $view = $this->createView('test_plugin.tpl');
        $view->registerPlugin('sum', function (array $params, \Ptf\View\Plain $view) {
            return $params[0] + $params[1];
        });
        $view->registerPlugin('helloWorld', function (\Ptf\View\Plain $view) {
            return 'hello world!';
        });
        $this->assertSame('plugin output: hello world! 42', $view->fetch());
    }

    private function createView($templateName = 'test.tpl')
    {
        $config = $this->createConfig();
        $view = new \Ptf\View\Plain($config);
        $view->setTemplateName($templateName);

        return $view;
    }

    private function createConfig()
    {
        $config = new \Ptf\App\Config\ViewPlain();
        $config->template_dir = __DIR__ . '/templates/Plain';

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

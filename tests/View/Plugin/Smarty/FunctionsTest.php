<?php

namespace Ptf\View\Plugin\Smarty;

use \Ptf\View\Plugin\Smarty\Functions;

class FunctionsTest extends \PHPUnit\Framework\TestCase
{
    public function testSid()
    {
        if (!defined('SID')) {
            define('SID', 'foobar');
        }
        $this->assertEquals(SID, Functions::sid());
    }

    public function testExec()
    {
        $context = \Ptf\Application::getContext();
        $view = new \Ptf\View\Smarty($this->createConfig(), $context);
        $context->setView($view);

        $_GET['foo'] = 'bar';
        $get = $_GET;
        $_REQUEST['bar'] = 'baz';
        $req = $_REQUEST;

        $context->getResponse()->setContent(null);

        \Ptf\Core\Router::matchRoute('functions_test/exec', $context);

        $compare = "exec: inner: one some value three\n"
            . "exec2: hello world\n"
            . "some value a string var 43";
        $this->assertSame($compare, $context->getView()->fetch());

        // Test if the response content is still empty (was temporarily changed by second embedded action)
        $this->assertFalse($context->getResponse()->hasContent());

        // Test if the template name has not been changed by the embedded actions
        $this->assertSame('test_exec.tpl', $context->getView()->getTemplateName());

        // Test if the REQUEST vars have not changed
        $this->assertSame($get, $_GET);
        $this->assertSame($req, $_REQUEST);

        unset($_GET['foo']);
        unset($_REQUEST['bar']);
    }

    private function createConfig()
    {
        $config = new \Ptf\App\Config\ViewSmarty();
        $config->template_dir = __DIR__ . '/../../templates/Smarty';
        $config->compile_check = 1;
        $config->compile_dir = $config->cache_dir = sys_get_temp_dir() . '/ptf_test';

        return $config;
    }
}

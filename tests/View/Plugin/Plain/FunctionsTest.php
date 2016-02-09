<?php

namespace Ptf\View\Plugin\Plain;

use \Ptf\View\Plugin\Plain\Functions;

class FunctionsTest extends \PHPUnit_Framework_TestCase
{
    public function testSid()
    {
        if (!defined('SID')) {
            define('SID', 'foobar');
        }
        $this->assertEquals(SID, Functions::sid());
    }

    public function testDblbr2p()
    {
        $input = "foo<br>bar<br><br>baz";
        $this->assertSame("foo<br>bar\n</p>\n<p>baz", Functions::dblbr2p($input));
        $input = "<br />foo<br/><br  >bar<br><br\t\t /><br />baz";
        $this->assertSame("<br />foo\n</p>\n<p>bar\n</p>\n<p><br />baz", Functions::dblbr2p($input));
        $input = "foo<br   ><br\t>bar<br />  <br><br >\n<br\t/>baz";
        $this->assertSame("foo\n</p>\n<p>bar\n</p>\n<p>\n</p>\n<p>baz", Functions::dblbr2p($input));
    }

    public function testExec()
    {
        $context = \Ptf\Application::getContext();
        $view = new \Ptf\View\Plain($this->createConfig(), $context);
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
        $config = new \Ptf\App\Config\ViewPlain();
        $config->template_dir = __DIR__ . '/../../templates/Plain';

        return $config;
    }
}

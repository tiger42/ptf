<?php

namespace PtfTest\App;

class Context extends \Ptf\App\Context
{
    protected function init()
    {
        $this->routingTable = [
                'bar'     => 'BaseTest/DummyAction',
                'FOO/Bar' => '/index',
                'baz'     => 'index'
        ];
        $this->loggers = [];

        $config = new \Ptf\App\Config\View();
        $this->view = new \PtfTest\View\Dummy($config, $this);

        // This is dirty...
        $_SERVER['argv'] = ['dummy', 'CliActionTest=ActionOutput'];
        $_SERVER['argc'] = count($_SERVER['argv']);
        $this->cliParams = new \Ptf\Core\Cli\Params();
        $this->cliOutput = new \Ptf\Core\Cli\Output();
    }

    public function getAppNamespace()
    {
        return 'PtfTest';
    }

    public function isCli()
    {
        return false;
    }

    public function setView($view)
    {
        $this->view = $view;
    }
}

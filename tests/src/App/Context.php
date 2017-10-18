<?php

namespace PtfTest\App;

class Context extends \Ptf\App\Context
{
    protected function init(): void
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

    public function getAppNamespace(): string
    {
        return 'PtfTest';
    }

    public function isCli(): bool
    {
        return false;
    }

    public function setView(\Ptf\View\Base $view): void
    {
        $this->view = $view;
    }
}

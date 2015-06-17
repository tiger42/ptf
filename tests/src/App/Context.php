<?php

namespace PtfTest\App;

class Context extends \Ptf\App\Context
{
    public function getAppNamespace()
    {
        return 'PtfTest';
    }

    protected function init()
    {
        $this->routingTable = [
                'bar'     => 'test/DummyAction',
                'FOO/Bar' => '/index',
                'baz'     => 'index'
        ];
        $this->loggers = [];

        $config = new \Ptf\App\Config\View();
        $this->view = new \PtfTest\View\Dummy($config);

        $this->request = new \Ptf\Core\Http\Request();
    }

}

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
                'bar'     => 'test/dummy',
                'FOO/Bar' => '/index',
                'baz'     => 'index'
        ];
        $this->loggers = [];
        $this->view = [];

        $this->request  = new \Ptf\Core\Http\Request();
    }

}

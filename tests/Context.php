<?php

namespace PtfTest;

class Context extends \Ptf\App\Context
{
    public function getAppNamespace()
    {
        return 'PtfTest';
    }

    protected function init()
    {
        $this->view = [];
    }

}

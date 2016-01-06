<?php

namespace PtfTest\Controller\BaseTest\Action;

class DummyAction extends \Ptf\Controller\Base\Action\Base
{
    public function execute()
    {
        echo 'BaseTest/DummyAction';
    }

}

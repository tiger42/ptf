<?php

namespace PtfTest\Controller\Test\Action;

class DummyAction extends \Ptf\Controller\Base\Action\Base
{
    public function execute(\Ptf\Core\Http\Request $request, \Ptf\Core\Http\Response $response)
    {
        $response->setContent('Test/DummyAction');
    }

}

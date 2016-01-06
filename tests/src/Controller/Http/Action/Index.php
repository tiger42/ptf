<?php

namespace PtfTest\Controller\Http\Action;

class Index extends \Ptf\Controller\Http\Action\Base
{
    public function execute(\Ptf\Core\Http\Request $request, \Ptf\Core\Http\Response $response)
    {
        $response->setContent($request->getGetVar('HttpActionTest'));
    }

}

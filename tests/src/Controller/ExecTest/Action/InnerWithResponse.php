<?php

namespace PtfTest\Controller\ExecTest\Action;

class InnerWithResponse extends \Ptf\Controller\Http\Action\Base
{
    public function execute(\Ptf\Core\Http\Request $request, \Ptf\Core\Http\Response $response): void
    {
        $response->setContent($request->getGetVar('param1') . ' world');
    }
}

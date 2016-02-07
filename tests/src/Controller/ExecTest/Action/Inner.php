<?php

namespace PtfTest\Controller\ExecTest\Action;

class Inner extends \Ptf\Controller\Http\Action\Base
{
    public function execute(\Ptf\Core\Http\Request $request, \Ptf\Core\Http\Response $response)
    {
        $context = \Ptf\Application::getContext();
        $view = $context->getView();

        $view['v1'] = 'one';
        $view['v2'] = $view['var1'];

        $view['assigned1'] = $request->getGetVar('param1') . ' var';
        $view->assigned2 = $request->getRequestVar('param2') + 1;

        $view->setTemplateName('test_exec_inner.tpl');
    }
}

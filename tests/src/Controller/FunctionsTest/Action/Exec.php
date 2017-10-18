<?php

namespace PtfTest\Controller\FunctionsTest\Action;

class Exec extends \Ptf\Controller\Http\Action\Base
{
    public function execute(\Ptf\Core\Http\Request $request, \Ptf\Core\Http\Response $response): void
    {
        $context = \Ptf\Application::getContext();
        $view = $context->getView();

        $view['v3'] = 'three';

        $view->setTemplateName('test_exec.tpl');
    }
}

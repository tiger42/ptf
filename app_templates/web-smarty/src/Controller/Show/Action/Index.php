<?php

namespace _NAMESPACE_\Controller\Show\Action;

/**
 * The action for the "show/index" route
 */
class Index extends \Ptf\Controller\Http\Action\Base
{
    /**
     * Execute the action
     *
     * @param   \Ptf\Core\Http\Request $request    The current request object
     * @param   \Ptf\Core\Http\Response $response  The response object
     */
    public function execute(\Ptf\Core\Http\Request $request, \Ptf\Core\Http\Response $response)
    {
        $context = \Ptf\Application::getContext();
        $view = $context->getView();

        $name = $request->getGetVar('name');
        if (!$name) {
            $name = 'World';
        }
        $view['name'] = $name;

        $view->setTemplateName('index.tpl');
    }
}

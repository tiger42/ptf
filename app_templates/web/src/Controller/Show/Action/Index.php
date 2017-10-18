<?php

namespace _NAMESPACE_\Controller\Show\Action;

use Ptf\Controller\Http\Action\Base as BaseAction;
use Ptf\Core\Http\{Request, Response};

/**
 * The action for the "show/index" route.
 */
class Index extends BaseAction
{
    /**
     * Execute the action.
     *
     * @param Request  $request   The current request object
     * @param Response $response  The response object
     */
    public function execute(Request $request, Response $response): void
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

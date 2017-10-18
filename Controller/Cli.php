<?php

namespace Ptf\Controller;

use Ptf\Controller\Base as BaseController;
use Ptf\Controller\Base\Action\Base as BaseAction;

/**
 * Base controller for all CLI applications.
 */
class Cli extends BaseController
{
    /**
     * Check if the given action has correctly been instantiated.
     *
     * @param mixed $action  The action to check
     *
     * @return bool  Does the action have the correct type?
     */
    protected function checkAction($action): bool
    {
        return $action instanceof \Ptf\Controller\Cli\Action\Base;
    }

    /**
     * Execute the given action.
     *
     * @param BaseAction $action  The action to execute
     */
    protected function executeAction(BaseAction $action): void
    {
        $action->execute($this->context->getCliParams(), $this->context->getCliOutput());
    }

    /**
     * Get the name of the controller's default action.
     *
     * @return string  The name of the default action
     */
    public function getDefaultActionName(): string
    {
        return 'Execute';
    }
}

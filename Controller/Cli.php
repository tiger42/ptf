<?php

namespace Ptf\Controller;

/**
 * Base controller for all CLI applications
 */
class Cli extends \Ptf\Controller\Base
{
    /**
     * Check if the given action has correctly been instantiated
     *
     * @param   mixed $action               The action to check
     * @return  boolean                     Does the action have the correct type?
     */
    protected function checkAction($action)
    {
        return $action instanceof \Ptf\Controller\Cli\Action\Base;
    }

    /**
     * Execute the given action
     *
     * @param   \Ptf\Controller\Base\Action\Base $action The action to execute
     */
    protected function executeAction(\Ptf\Controller\Base\Action\Base $action)
    {
        $action->execute($this->context->getCliParams(), $this->context->getCliOutput());
    }

    /**
     * Get the name of the controller's default action
     *
     * @return  string                      The name of the default action
     */
    public function getDefaultActionName()
    {
        return 'Execute';
    }

}

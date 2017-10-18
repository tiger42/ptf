<?php

namespace _NAMESPACE_\Controller;

/**
 * The Controller for the "task/*" route.
 */
class Task extends \Ptf\Controller\Cli
{
    /**
     * Get the name of the controller's default action.
     *
     * @return string  The name of the default action
     */
    public function getDefaultActionName(): string
    {
        return 'Run';
    }
}

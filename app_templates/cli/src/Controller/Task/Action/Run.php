<?php

namespace _NAMESPACE_\Controller\Task\Action;

use Ptf\Controller\Cli\Action\Base as BaseAction;
use Ptf\Core\Cli\{Params, Output};

/**
 * The action for the "task/run" route.
 */
class Run extends BaseAction
{
    /**
     * Execute the action.
     *
     * @param Params $params  The current parameters object
     * @param Output $output  The output object
     */
    public function execute(Params $params, Output $output): void
    {
        $name = $params->has('--name') ? $params->get('--name')
            : ($params->has('-n') ? $params->get('-n') : 'World');

        $output->setContent("Hello $name!\n");
    }
}

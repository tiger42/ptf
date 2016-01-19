<?php

namespace _NAMESPACE_\Controller\Task\Action;

/**
 * The action for the "task/run" route
 */
class Run extends \Ptf\Controller\Cli\Action\Base
{
    /**
     * Execute the action
     *
     * @param   \Ptf\Core\Cli\Params $params  The current parameters object
     * @param   \Ptf\Core\Cli\Output $output  The response object
     */
    public function execute(\Ptf\Core\Cli\Params $params, \Ptf\Core\Cli\Output $output)
    {
        $name = $params->has('--name') ? $params->get('--name')
            : ($params->has('-n') ? $params->get('-n') : 'World');

        $output->setContent("Hello $name!\n");
    }
}

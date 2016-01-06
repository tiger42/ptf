<?php

namespace Ptf\Controller\Cli\Action;

/**
 * The base action for all CLI actions to be extended
 */
abstract class Base extends \Ptf\Controller\Base\Action\Base
{
    /**
     * Execute the action
     *
     * @param   \Ptf\Core\Cli\Params $params The current parameters object
     * @param   \Ptf\Core\Cli\Output $output The response object
     */
    abstract public function execute(\Ptf\Core\Cli\Params $params, \Ptf\Core\Cli\Output $output);

}

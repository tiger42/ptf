<?php

namespace PtfTest\Controller\Cli\Action;

class Execute extends \Ptf\Controller\Cli\Action\Base
{
    public function execute(\Ptf\Core\Cli\Params $params, \Ptf\Core\Cli\Output $output): void
    {
        $output->setContent($params->get('CliActionTest'));
    }
}

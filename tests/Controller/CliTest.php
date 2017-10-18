<?php

namespace Ptf\Controller;

use \Ptf\Controller\Cli as CliController;

class CliTest extends \PHPUnit\Framework\TestCase
{
    public function testCheckAction()
    {
        $context = \Ptf\Application::getContext();

        $controller = new MyCliController('Base', $context);
        $action = new \PtfTest\Controller\Cli\Action\Execute('Execute', $controller);
        $this->assertTrue($controller->checkAction($action));

        $action = new \PtfTest\Controller\Base\Action\Index('Execute', $controller);
        $this->assertFalse($controller->checkAction($action));

        $this->assertFalse($controller->checkAction(null));
    }

    public function testExecuteAction()
    {
        $context = \Ptf\Application::getContext();

        $controller = new MyCliController('Base', $context);
        $action = new \PtfTest\Controller\Cli\Action\Execute('Execute', $controller);

        $controller->executeAction($action);
        // The test CLI parameters are already set in the Context class...
        $this->assertSame('ActionOutput', $context->getCliOutput()->getContent());
    }

    public function testGetDefaultActionName()
    {
        $context = \Ptf\Application::getContext();

        $controller = new CliController('Foobar', $context);
        $this->assertSame('Execute', $controller->getDefaultActionName());
    }
}

class MyCliController extends CliController
{
    public function checkAction($action): bool
    {
        return parent::checkAction($action);
    }

    public function executeAction(\Ptf\Controller\Base\Action\Base $action): void
    {
        parent::executeAction($action);
    }
}

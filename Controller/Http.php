<?php

namespace Ptf\Controller;

use Ptf\Controller\Base as BaseController;
use Ptf\Controller\Base\Action\Base as BaseAction;
use Ptf\Core\Exception\SystemExit as SystemExitException;

/**
 * Base controller for all web applications.
 */
class Http extends BaseController
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
        return $action instanceof \Ptf\Controller\Http\Action\Base;
    }

    /**
     * Execute the given action.
     *
     * @param BaseAction $action  The action to execute
     */
    protected function executeAction(BaseAction $action): void
    {
        $action->execute($this->context->getRequest(), $this->context->getResponse());
    }

    /**
     * Forward to the current view's configured 404 page.<br />
     * This function will terminate the application!
     *
     * @throws SystemExitException  Always
     */
    public function forward404(): void
    {
        $this->context->getResponse()
            ->set404Header()
            ->setContent($this->context->getView()->fetch404Page())
            ->send();
        throw new SystemExitException();
    }

    /**
     * Do a HTTP redirect to the given URL.<br />
     * This function will terminate the application!
     *
     * @param string $url           The URL to redirect to
     * @param int    $responseCode  The HTTP response code to send
     *
     * @throws SystemExitException  Always
     */
    public function redirect(string $url, int $responseCode = 302): void
    {
        $this->context->getResponse()
            ->setRedirectHeader($url, $responseCode)
            ->sendHeaders();
        throw new SystemExitException();
    }
}

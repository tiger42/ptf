<?php

namespace Ptf\Controller;

/**
 * Base controller for all web applications
 */
class Http extends \Ptf\Controller\Base
{
    /**
     * Check if the given action has correctly been instantiated
     *
     * @param   mixed $action               The action to check
     * @return  boolean                     Does the action have the correct type?
     */
    protected function checkAction($action)
    {
        return $action instanceof \Ptf\Controller\Http\Action\Base;
    }

    /**
     * Execute the given action
     *
     * @param   \Ptf\Controller\Base\Action\Base $action  The action to execute
     */
    protected function executeAction(\Ptf\Controller\Base\Action\Base $action)
    {
        $action->execute($this->context->getRequest(), $this->context->getResponse());
    }

    /**
     * Forward to the current view's configured 404 page.<br />
     * This function will terminate the application!
     *
     * @throws  \Ptf\Core\Exception\SystemExit
     */
    public function forward404()
    {
        $this->context->getResponse()
            ->set404Header()
            ->setContent($this->context->getView()->fetch404Page())
            ->send();
        throw new \Ptf\Core\Exception\SystemExit();
    }

    /**
     * Do a HTTP redirect to the given URL.<br />
     * This function will terminate the application!
     *
     * @param   string $url                 The URL to redirect to
     * @param   integer $responseCode       The HTTP response code to send
     * @throws  \Ptf\Core\Exception\SystemExit
     */
    public function redirect($url, $responseCode = 302)
    {
        $this->context->getResponse()
            ->setRedirectHeader($url, $responseCode)
            ->sendHeaders();
        throw new \Ptf\Core\Exception\SystemExit();
    }
}

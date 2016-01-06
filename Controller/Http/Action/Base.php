<?php

namespace Ptf\Controller\Http\Action;

/**
 * The base action for all HTTP actions to be extended
 */
abstract class Base extends \Ptf\Controller\Base\Action\Base
{
    /**
     * Forward to the current view's configured 404 page
     */
    public function forward404()
    {
        $this->controller->forward404();
    // @codeCoverageIgnoreStart
    }
    // @codeCoverageIgnoreEnd

    /**
     * Do a HTTP redirect to the given URL
     *
     * @param   string $url                 The URL to redirect to
     * @param   integer $responseCode       The HTTP response code to send
     */
    public function redirect($url, $responseCode = 302)
    {
        $this->controller->redirect($url, $responseCode);
    // @codeCoverageIgnoreStart
    }
    // @codeCoverageIgnoreEnd

    /**
     * Execute the action
     *
     * @param   \Ptf\Core\Http\Request $request   The current request object
     * @param   \Ptf\Core\Http\Response $response The response object
     */
    abstract public function execute(\Ptf\Core\Http\Request $request, \Ptf\Core\Http\Response $response);

}

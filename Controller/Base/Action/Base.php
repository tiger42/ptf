<?php

namespace Ptf\Controller\Base\Action;

/**
 * The Base action for all actions to be extended
 */
abstract class Base
{
    /**
     * The controller owning the action
     * @var \Ptf\Controller\Base
     */
    protected $controller;

    /**
     * Initialize the member variables
     *
     * @param   \Ptf\Controller\Base $controller The action's controller
     */
    public function __construct(\Ptf\Controller\Base $controller)
    {
        $this->controller = $controller;
    }

    /**
     * Forward to the given route
     *
     * @param   string $route               The route to forward to
     */
    public function forward($route)
    {
        $this->controller->forward($route);
    }

    /**
     * Forward to the current view's configured 404 page
     */
    public function forward404()
    {
        $this->controller->forward404();
    }

    /**
     * Do a HTTP redirect to the given URL
     *
     * @param   string $url                 The URL to redirect to
     * @param   integer $responseCode       The HTTP response code to send
     */
    public function redirect($url, $responseCode = 302)
    {
        $this->controller->redirect($url, $responseCode);
    }

    /**
     * Execute the action
     *
     * @param   \Ptf\Core\Http\Request $request   The current request object
     * @param   \Ptf\Core\Http\Response $response The response object
     */
    abstract public function execute(\Ptf\Core\Http\Request $request, \Ptf\Core\Http\Response $response);

}

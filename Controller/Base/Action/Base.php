<?php

namespace Ptf\Controller\Base\Action;

/**
 * The Base action for all actions to be extended
 */
abstract class Base
{
    /**
     * The action's name
     * @var string
     */
    protected $name;
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
    public function __construct($actionName, \Ptf\Controller\Base $controller)
    {
        $this->name = $actionName;
        $this->controller = $controller;
    }

    /**
     * Return the action's name
     *
     * @return  string                      The name of the action
     */
    public function getName()
    {
        return $this->name;
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

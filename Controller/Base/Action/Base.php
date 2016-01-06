<?php

namespace Ptf\Controller\Base\Action;

/**
 * The base action for all actions
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

}

<?php

namespace Ptf\Controller\Base\Action;

/**
 * The base action for all actions.
 */
abstract class Base
{
    /** @var string  The action's name */
    protected $name;

    /** @var \Ptf\Controller\Base  The controller owning the action */
    protected $controller;

    /**
     * Initialize the member variables.
     *
     * @param string               $actionName  The name of the action
     * @param \Ptf\Controller\Base $controller  The action's controller
     */
    public function __construct(string $actionName, \Ptf\Controller\Base $controller)
    {
        $this->name = $actionName;
        $this->controller = $controller;
    }

    /**
     * Return the action's name.
     *
     * @return string  The name of the action
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Forward to the given route.
     *
     * @param string $route  The route to forward to
     */
    public function forward(string $route): void
    {
        $this->controller->forward($route);
    }
}

<?php

namespace Ptf\Controller;

/**
 * The base controller for all applications
 */
class Base
{
    /**
     * The controller's name
     * @var string
     */
    protected $name;
    /**
     * The application's context
     * @var \Ptf\App\Context
     */
    protected $context;
    /**
     * The called action
     * @var \Ptf\Controller\Base\Action\Base
     */
    protected $action;

    /**
     * Initialize the member variables
     *
     * @param   string $controllerName      The name of the controller to set
     * @param   \Ptf\App\Context $context   The application's context
     */
    public function __construct($controllerName, \Ptf\App\Context $context)
    {
        $this->name    = $controllerName;
        $this->context = $context;
        $this->action  = null;
    }

    /**
     * Dispatch the given action of the controller
     *
     * @param   string $actionName                The name of the action to dispatch
     * @throws  \Ptf\Core\Exception\InvalidAction If the given action does not exist
     * @throws  \Exception                        If the Action object could not be instantiated
     */
    final public function dispatch($actionName = null)
    {
        if (!$actionName) {
            $actionName = $this->getDefaultActionName();
        }
        $actionName = \Ptf\Util\camelize($actionName);

        $className = $this->context->getAppNamespace() . '\\Controller\\' . $this->name . '\\Action\\' . $actionName;
        if (!class_exists($className)) {
            throw new \Ptf\Core\Exception\InvalidAction(get_class($this) . "::" . __FUNCTION__ . ": Action class not found: " . $className);
        }

        $action = new $className($actionName, $this);
        if (!$this->checkAction($action)) {
            throw new \Exception(get_class($this) . "::" . __FUNCTION__ . ": Action must extend base action: " . $className);
        }
        $this->action = $action;
        $this->context->getLogger()->logSys(get_class($this) . "::" . __FUNCTION__, "Executing action: " . $className);

        $this->executeAction($action);
    }

    /**
     * Check if the given action has correctly been instantiated
     *
     * @param   mixed $action               The action to check
     * @return  boolean                     Does the action have the correct type?
     */
    protected function checkAction($action)
    {
        return $action instanceof \Ptf\Controller\Base\Action\Base;
    }

    /**
     * Execute the given action
     *
     * @param   \Ptf\Controller\Base\Action\Base $action The action to execute
     */
    protected function executeAction(\Ptf\Controller\Base\Action\Base $action)
    {
        $action->execute();
    }

    /**
     * Get the controller's name
     *
     * @return  string                      The name of the controller
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Get the name of the controller's default action
     *
     * @return  string                      The name of the default action
     */
    public function getDefaultActionName()
    {
        return 'Index';
    }

    /**
     * Get the called action
     *
     * @return  \Ptf\Controller\Base\Action\Base The called action
     */
    public function getAction()
    {
        return $this->action;
    }

    /**
     * Forward to the given route
     *
     * @param   string $route               The route to forward to
     * @return  \Ptf\Controller\Base        The controller handling the route
     */
    final public function forward($route)
    {
        $this->context->getLogger()->logSys(get_class($this) . "::" . __FUNCTION__, "Forwarding to: " . $route);

        // Only an action (w/o controller) was given
        if (strpos($route, '/') === false) {
            $this->dispatch($route);

            return $this;

        // Given controller is current controller
        } elseif (strpos(strtolower($route), strtolower($this->name) . '/') === 0) {
            $parts = explode('/', $route);
            $this->dispatch($parts[1]);

            return $this;

        // Forward to other controller
        } else {
            return \Ptf\Core\Router::matchRoute($route, $this->context);
        }
    }

}

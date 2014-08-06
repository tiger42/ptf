<?php

namespace Ptf\Controller;

/**
 * Simple base controller
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
    public function dispatch($actionName)
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
        if (!($action instanceof \Ptf\Controller\Base\Action\Base)) {
            throw new \Exception(get_class($this) . "::" . __FUNCTION__ . ": Action must extend base action: " . $className);
        }
        $this->action = $action;
        $this->context->getLogger()->logSys(get_class($this) . "::" . __FUNCTION__, "Executing action: " . $className);

        $action->execute($this->context->getRequest(), $this->context->getResponse());
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
     */
    public function forward($route)
    {
        $this->context->getLogger()->logSys(get_class($this) . "::" . __FUNCTION__, "Forwarding to: " . $route);

        // Only an action (w/o controller) was given or controller is current controller
        if (strpos($route, '/') === false
            || strpos(strtolower($route), strtolower($this->name) . '/') === 0)
        {
            $this->dispatch($route);
        } else {
            \Ptf\Core\Router::matchRoute($route, $this->context);
        }
    }

    /**
     * Forward to the current view's configured 404 page.<br />
     * This function will terminate the application!
     */
    public function forward404()
    {
        $this->context->getResponse()
            ->set404Header()
            ->setContent($this->context->getView()->fetch404Page())
            ->send();
        exit;
    }

    /**
     * Do a HTTP redirect to the given URL.<br />
     * This function will terminate the application!
     *
     * @param   string $url                 The URL to redirect to
     * @param   integer $responseCode       The HTTP response code to send
     */
    public function redirect($url, $responseCode = 302)
    {
        $this->context->getResponse()
            ->setRedirectHeader($url, $responseCode)
            ->sendHeaders();
        exit;
    }

}

<?php

namespace Ptf\Controller;

use Ptf\App\Context;
use Ptf\Controller\Base as BaseController;

/**
 * Factory for producing Controller objects.
 */
class Factory
{
    /**
     * Create a Controller object defined by the given name.
     *
     * @param string  $controllerName  The name of the controller to create
     * @param Context $context         The application's context
     *
     * @throws \Exception  If the Controller object could not be instantiated
     *
     * @return BaseController  The created Controller object
     */
    public static function createController(string $controllerName, Context $context): BaseController
    {
        if (!$controllerName) {
            $controllerName = $context->getDefaultControllerName();
        }
        $controllerName = \Ptf\Util\camelize($controllerName);

        $className = $context->getAppNamespace() . '\\Controller\\' . $controllerName;

        if (!class_exists($className)) {
            $className = $context->isCli() ? '\\Ptf\\Controller\\Cli' : '\\Ptf\\Controller\\Http';

            // FIXME: This is just for unit tests, find a better solution!
            if (property_exists($context, 'controllerType')) {
                $className = '\\Ptf\\Controller\\' . $context->controllerType;
            }
        }

        $controller = new $className($controllerName, $context);
        if (!($controller instanceof BaseController)) {
            throw new \Exception(__METHOD__ . ": Controller must extend base controller: " . $className);
        }
        $context->getLogger()->logSys(__METHOD__, "Controller created: " . $className);

        $context->_setController($controller);

        return $controller;
    }
}

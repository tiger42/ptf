<?php

namespace Ptf\Controller;

/**
 * Factory for producing Controller objects
 */
class Factory
{
    /**
     * Create a Controller object defined by the given name
     *
     * @param   string $controllerName      The name of the controller to create
     * @param   \Ptf\App\Context $context   The application's context
     * @return  \Ptf\Controller\Base        The created Controller object
     * @throws  \Exception                  If the Controller object could not be instantiated
     */
    public static function createController($controllerName, \Ptf\App\Context $context)
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
        if (!($controller instanceof \Ptf\Controller\Base)) {
            throw new \Exception(__METHOD__ . ": Controller must extend base controller: " . $className);
        }
        $context->getLogger()->logSys(__METHOD__, "Controller created: " . $className);

        $context->setController($controller);

        return $controller;
    }
}

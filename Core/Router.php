<?php

namespace Ptf\Core;

/**
 * Class for handling the URL path to controller/action routing.
 */
class Router
{
    /**
     * Match the given route string to the corresponding controller and action.
     *
     * @param string           $route    The route string to match (case insensitive)
     * @param \Ptf\App\Context $context  The application's context
     *
     * @return \Ptf\Controller\Base|false  The matching controller or FALSE if route could not be resolved
     */
    public static function matchRoute(?string $route, \Ptf\App\Context $context)
    {
        $context->getLogger()->logSys(__METHOD__, 'Trying to match route: ' . $route);

        $parts = self::lookup($route, $context);
        $controllerName = $parts[0];
        $actionName     = $parts[1] ?? null;

        $controller = \Ptf\Controller\Factory::createController($controllerName, $context);
        try {
            $controller->dispatch($actionName);
        } catch (Exception\InvalidAction $e) {
            $context->getLogger('error')->logSys(__METHOD__, $e->getMessage(), \Ptf\Util\Logger::ERROR);
            return false;
        }
        return $controller;
    }

    /**
     * Match the route given in the request to the corresponding controller and action.
     *
     * @param \Ptf\App\Context $context  The application's context
     *
     * @return \Ptf\Controller\Base|false  The matching controller or FALSE if route could not be resolved
     */
    public static function matchRequestRoute(\Ptf\App\Context $context)
    {
        $request = $context->getRequest();

        $controllerName = $request->getRequestVar('controller');
        $actionName     = $request->getRequestVar('action');

        return static::matchRoute($controllerName . '/' . $actionName, $context);
    }

    /**
     * Look up the given route in the route mapping table.
     *
     * @param string           $route    The route string to look up (case insensitive)
     * @param \Ptf\App\Context $context  The application's context
     *
     * @return string[]  The mapped route if the string was found in the table
     */
    private static function lookup(string $route, \Ptf\App\Context $context): array
    {
        $routingTable = $context->getRoutingTable();
        $newRoute = strtolower($route);

        // We have to do this in a loop, as array_key_exists is case sensitive
        foreach ($routingTable as $source => $target) {
            if (strtolower($source) == $newRoute) {
                $route = $target;
                $context->getLogger()->logSys(__METHOD__, 'Mapping route to: ' . $newRoute);
                break;
            }
        }
        return explode('/', $route);
    }
}

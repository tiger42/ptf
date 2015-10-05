<?php

namespace Ptf;

if (php_sapi_name() != 'cli' && !defined('STDIN')) {
    exit;
}

require_once 'Application.php';

/**
 * Base class for all Ptf CLI applications
 */
abstract class CliApplication extends Application
{
    /**
     * Run the application
     */
    public static function run()
    {
        static::initAutoloader(Core\Autoloader::getInstance());

        $context = static::getContext();

        $args = $_SERVER['argv'];
        $route = str_replace(':', '/', $args[1]);
        if (!Core\Router::matchRoute($route, $context)) {
            echo "Unknown action: \"" . $args[1] . "\"\n";
            return;
        }
        $response = $context->getResponse();
        if ($response->hasContent()) {
            echo $response->getContent();
        } else {
            echo $context->getView()->fetch();
        }
    }

}

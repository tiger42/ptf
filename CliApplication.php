<?php

namespace Ptf;

if (php_sapi_name() != 'cli' && !defined('STDIN')) {
    exit;
}

require_once 'Application.php';

/**
 * Base class for all Ptf CLI applications.
 */
abstract class CliApplication extends Application
{
    /**
     * Run the application.
     */
    public static function run(): void
    {
        static::initAutoloader(Core\Autoloader::getInstance());

        /* @var $context App\Context */
        $context = static::getContext();

        $args = $_SERVER['argv'];
        $route = isset($args[1]) && strpos($args[1], ':') !== false ? str_replace(':', '/', $args[1]) : null;
        if (!Core\Router::matchRoute($route, $context)) {
            echo "Unknown command \"{$args[1]}\"\n\n";
            static::showUsage($args);
            return;
        }
        $output = $context->getCliOutput();
        if ($output->hasContent()) {
            echo $output->getContent();
        } else {
            echo $context->getView()->fetch();
        }
    }

    /**
     * Display a usage message for the application.<br />
     * Overwrite this function to display an individual message.
     */
    public static function showUsage(): void
    {
        $context = static::getContext();

        echo "Usage: php {$context->getBasePath(true)} controller:action [options]\n";
    }
}

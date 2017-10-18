<?php

namespace _NAMESPACE_;

// Set the path to the Ptf framework here
require_once __DIR__. '/../ptf/CliApplication.php';

/**
 * The application's main class.
 */
class Application extends \Ptf\CliApplication
{
    /**
     * Return the application's context object.
     *
     * @return \_NAMESPACE_\App\Context  The context of the application
     */
    public static function getContext(): \Ptf\App\Context
    {
        return \_NAMESPACE_\App\Context::getInstance();
    }

    /**
     * Initialize the autoloader.
     *
     * @param \Ptf\Core\Autoloader $autoloader  The autoloader to initialize
     */
    protected static function initAutoloader(\Ptf\Core\Autoloader $autoloader): void
    {
        $autoloader->registerNamespace('_NAMESPACE_', __DIR__);   // Register our own namespace
//        $autoloader->addOverrideDir('src/override');   // Register a directory for Ptf class overrides
        $autoloader->setCacheFilename(dirname($_SERVER['SCRIPT_FILENAME']) . '/var/autoload_cache.php');
    }

    /**
     * Display a usage message for the application.
     */
    public static function showUsage(): void
    {
        $context = static::getContext();

        echo "Usage:\n";
        echo "  php {$context->getBasePath(true)} command [--name=NAME]\n";
        echo "\nCommands:\n";
        echo "  task:run  Run the example task\n\n";
        echo "Options:\n";
        echo "  -n NAME, --name=NAME  The name to display\n";
    }
}

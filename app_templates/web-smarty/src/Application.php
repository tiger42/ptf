<?php

namespace _NAMESPACE_;

// Set the path to the Ptf framework here
require_once 'ptf/Application.php';

/**
 * The application's main class
 */
class Application extends \Ptf\Application
{
    /**
     * Return the application's context object
     *
     * @return  \_NAMESPACE_\App\Context  The context of the application
     */
    public static function getContext()
    {
        return \_NAMESPACE_\App\Context::getInstance();
    }

    /**
     * Initialize the autoloader
     *
     * @param   \Ptf\Core\Autoloader $autoloader  The autoloader to initialize
     */
    protected static function initAutoloader(\Ptf\Core\Autoloader $autoloader)
    {
        $autoloader->registerNamespace('_NAMESPACE_', 'src');   // Register our own namespace
//        $autoloader->addOverrideDir('src/override');   // Register a directory for Ptf class overrides
        $autoloader->setCacheFilename(dirname($_SERVER['SCRIPT_FILENAME']) . '/var/autoload_cache.php');
    }
}

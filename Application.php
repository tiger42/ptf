<?php

namespace Ptf;

if (!ini_get('date.timezone')) {
    date_default_timezone_set('UTC');
}
mb_internal_encoding('UTF-8');

const BASEDIR = __DIR__;

require_once 'Util/functions.php';
require_once 'Core/Autoloader.php';

/**
 * Base class for all Ptf applications
 */
abstract class Application
{
    /**
     * Run the application
     */
    public static function run()
    {
        static::initAutoloader(Core\Autoloader::getInstance());

        $context = static::getContext();

        try {
            if (!Core\Router::matchRequestRoute($context)) {
                $context->getController()->forward404();   // This will throw a SystemExit exception
            }
            $response = $context->getResponse();
            if (!$response->hasContent()) {
                $response->setContent($context->getView()->fetch());
            }
            $response->send();
        } catch (Core\Exception\SystemExit $e) {
        }
    }

    /**
     * Return the application's context object.<br>
     * Overwrite this method to return a custom context.
     *
     * @return  \Ptf\App\Context            The context of the application
     */
    public static function getContext()
    {
        return App\Context::getInstance();
    }

    /**
     * Initialize the autoloader.<br>
     * Overwrite this method to set an application specific autoload configuration.
     *
     * @param   \Ptf\Core\Autoloader $autoloader The autoloader to initialize
     */
    protected static function initAutoloader(Core\Autoloader $autoloader)
    {
        $autoloader->setCacheFilename(dirname($_SERVER['SCRIPT_FILENAME']) . '/autoload_cache.php');
    }

    /**
     * Compile the given config INI file into \Ptf\App\Config class files
     *
     * @param   string $configName          The filename of the config file
     * @param   string $configDir           The target directory for the generated class files
     * @param   string $namespace           The namespace of the application
     */
    public static function compileConfig($configName, $configDir, $namespace)
    {
        Util\ConfigCompiler::compile($configName, $configDir, $namespace);
    }

}

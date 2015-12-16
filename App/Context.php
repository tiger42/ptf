<?php

namespace Ptf\App;

use Ptf\Application;

/**
 * Abstract application context
 */
abstract class Context
{
    use \Ptf\Traits\Singleton;

    /**
     * The request object
     * @var \Ptf\Core\Http\Request
     */
    protected $request;
    /**
     * The response object
     * @var \Ptf\Core\Http\Response
     */
    protected $response;
    /**
     * The application's view object
     * @var \Ptf\View\Base
     */
    protected $view;
    /**
     * Array of configured loggers
     * @var \Ptf\Util\Logger[]
     */
    protected $loggers;
    /**
     * The called controller
     * @var \Ptf\Controller\Base
     */
    protected $controller;
    /**
     * Array of the application's configuration objects
     * @var \Ptf\App\Config[]
     */
    protected $configs;
    /**
     * The route mapping table
     * @var array
     */
    protected $routingTable;

    /**
     * Initialize the member variables
     */
    protected function __construct()
    {
        $this->request  = new \Ptf\Core\Http\Request();
        $this->response = new \Ptf\Core\Http\Response();
        $this->configs  = [];
        $this->routingTable = [];

        $this->initLoggers();
        $this->init();

        $view = $this->getView();
        $view['context'] = $this;
        $view['request'] = $this->getRequest();
    }

    /**
     * Overwrite this method to initialize application specific objects (loggers, view, ...)
     */
    protected function init()
    {
    }

    /**
     * Initialize the Logger objects
     */
    protected function initLoggers()
    {
        $logLevelLimit = (int)$this->getConfig('General')->getLogLevel();
        $this->loggers = [
            'system' => \Ptf\Util\Logger\File::getInstance('var/log/system.log', $this, $logLevelLimit),
            'error'  => \Ptf\Util\Logger\File::getInstance('var/log/error.log', $this, $logLevelLimit)
        ];
    }

    /**
     * Get the application's namespace
     *
     * @return  string                      The namespace of the application
     */
    abstract public function getAppNamespace();

    /**
     * Get the name of the default controller
     *
     * @return  string                      The name of the default controller
     */
    public function getDefaultControllerName()
    {
        return 'Base';
    }

    /**
     * Get the request object
     *
     * @return  \Ptf\Core\Http\Request      The request object
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * Get the response object
     *
     * @return  \Ptf\Core\Http\Response     The response object
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * Get the application's view object
     *
     * @return  \Ptf\View\Base              The view used by the application
     */
    public function getView()
    {
        if ($this->view === null) {
            // Set fallback view if none is set in the init() function
            $this->view = new \Ptf\View\Plain($this->getConfig('ViewPlain'));
        }
        return $this->view;
    }

    /**
     * Get the logger with the given name
     *
     * @return  \Ptf\Util\Logger            The requested logger or a dummy logger, if a logger with the given name does not exist
     */
    public function getLogger($logger = 'system')
    {
        return isset($this->loggers[$logger]) ? $this->loggers[$logger] : \Ptf\Util\Logger\DevNull::getInstance('dummy', $this);
    }

    /**
     * Set the called controller
     *
     * @param   \Ptf\Controller\Base $controller The controller to set
     */
    public function setController(\Ptf\Controller\Base $controller)
    {
        $this->controller = $controller;
    }

    /**
     * Get the called controller
     *
     * @return  \Ptf\Controller\Base        The called controller
     */
    public function getController()
    {
        return $this->controller;
    }

    /**
     * Get the configuration object with the given name
     *
     * @param   string $configName          The name of the configuration to fetch
     * @return  \Ptf\App\Config             The requested configuration object
     * @throws  \RuntimeException           If the requested config object does not exist
     */
    public function getConfig($configName = 'General')
    {
        if (!isset($this->configs[$configName])) {
            $className = $this->getAppNamespace() . '\\App\\Config\\' . $configName;
            if (class_exists($className)) {
                $this->configs[$configName] = new $className();
            } else {
                $className = '\\Ptf\\App\\Config\\' . $configName;
                if (!class_exists($className)) {
                    throw new \RuntimeException(get_class($this) . "::" . __FUNCTION__
                        . ": Configuration not found: " . $configName);
                }
                $this->configs[$configName] = new $className();
            }
        }
        return $this->configs[$configName];
    }

    /**
     * Get the route mapping table
     *
     * @return  array                       The route mapping table
     */
    public function getRoutingTable()
    {
        return $this->routingTable;
    }

    /**
     * Get the application's base path
     *
     * @param   string $withScriptName      Append the filename of the bootstrap script?
     * @return  string                      The base path
     */
    public function getBasePath($withScriptName = false)
    {
        return $withScriptName ? $_SERVER['PHP_SELF'] : dirname($_SERVER['PHP_SELF']);
    }

    /**
     * Get the application's base URL
     *
     * @param   string $withScriptName      Append the filename of the bootstrap script?
     * @return  string                      The base URL
     */
    public function getBaseUrl($withScriptName = false)
    {
        if (!$this->isCli()) {
            $url = strtolower($this->request->getProtocol()) . '://' . $this->request->getHost();
        }

        $url .= $this->getBasePath($withScriptName);

        return $url;
    }

    /**
     * Determine, whether the application was called from the command line interface
     *
     * @return  boolean                     Was the application called from CLI?
     */
    public function isCli()
    {
        return php_sapi_name() == 'cli' || defined('STDIN');
    }

}

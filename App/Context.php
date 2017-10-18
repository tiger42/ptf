<?php

namespace Ptf\App;

use Ptf\Core\Cli;
use Ptf\Core\Http;

/**
 * Abstract application context.
 */
abstract class Context
{
    use \Ptf\Traits\Singleton;

    /**
     * The request object (for web applications only)
     * @var Http\Request
     */
    protected $request;

    /**
     * The response object (for web applications only)
     * @var Http\Response
     */
    protected $response;

    /**
     * The command line parameters (for CLI applications only)
     * @var Cli\Params
     */
    protected $cliParams;

    /**
     * The console output (for CLI applications only)
     * @var Cli\Output
     */
    protected $cliOutput;

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
     * @var Config[]
     */
    protected $configs;

    /**
     * The route mapping table
     * @var array
     */
    protected $routingTable;

    /**
     * Initialize the member variables.
     */
    protected function __construct()
    {
        if ($this->isCli()) {
            $this->cliParams = new Cli\Params();
            $this->cliOutput = new Cli\Output();
        } else {
            $this->request  = new Http\Request();
            $this->response = new Http\Response();
        }

        $this->configs = [];
        $this->routingTable = [];

        $this->initLoggers();
        $this->init();

        if (!$this->isCli()) {
            $view = $this->getView();
            $view['request'] = $this->getRequest();
        }
    }

    /**
     * Overwrite this method to initialize application specific objects (loggers, view, ...).
     */
    protected function init(): void
    {
    }

    /**
     * Initialize the Logger objects
     */
    protected function initLoggers(): void
    {
        $config = $this->getConfig('General');

        $logLevelLimit = (int)$config->getLogLevel();
        $sysLog = $config->getSystemLog();
        $errLog = $config->getErrorLog();

        $this->loggers = [
            'system' => \Ptf\Util\Logger\File::getInstance($sysLog, $this, $logLevelLimit),
            'error'  => \Ptf\Util\Logger\File::getInstance($errLog, $this, $logLevelLimit)
        ];
    }

    /**
     * Get the application's namespace.
     *
     * @return string  The namespace of the application
     */
    abstract public function getAppNamespace(): string;

    /**
     * Get the name of the default controller.
     *
     * @return string  The name of the default controller
     */
    public function getDefaultControllerName(): string
    {
        return 'Base';
    }

    /**
     * Get the request object.
     *
     * @return Http\Request  The request object
     */
    public function getRequest(): ?Http\Request
    {
        return $this->request;
    }

    /**
     * Get the response object.
     *
     * @return Http\Response The response object
     */
    public function getResponse(): ?Http\Response
    {
        return $this->response;
    }

    /**
     * Get the CLI parameters object.
     *
     * @return Cli\Params  The CLI parameters object
     */
    public function getCliParams(): ?Cli\Params
    {
        return $this->cliParams;
    }

    /**
     * Get the console output object.
     *
     * @return Cli\Output  The CLI output object
     */
    public function getCliOutput(): ?Cli\Output
    {
        return $this->cliOutput;
    }

    /**
     * Get the application's view object.
     *
     * @return \Ptf\View\Base  The view used by the application
     */
    public function getView(): \Ptf\View\Base
    {
        if ($this->view === null) {
            // Set fallback view if none is set in the init() function
            $this->view = new \Ptf\View\Plain($this->getConfig('ViewPlain'), $this);
        }
        return $this->view;
    }

    /**
     * Get the logger with the given name.
     *
     * @param string $logger  The name of the logger to fetch
     *
     * @return \Ptf\Util\Logger  The requested logger or a dummy logger, if a logger with the given name does not exist
     */
    public function getLogger(string $logger = 'system'): \Ptf\Util\Logger
    {
        return $this->loggers[$logger] ?? \Ptf\Util\Logger\DevNull::getInstance('dummy', $this);
    }

    /**
     * Get the called controller.
     *
     * @return \Ptf\Controller\Base  The called controller
     */
    public function getController(): \Ptf\Controller\Base
    {
        return $this->controller;
    }

    /**
     * Set the called controller.<br />
     * This function is only for internal framework purposes!
     * @ignore
     *
     * @param \Ptf\Controller\Base $controller  The controller to set
     */
    public function _setController(\Ptf\Controller\Base $controller): void
    {
        $this->controller = $controller;
    }

    /**
     * Get the configuration object with the given name.
     *
     * @param string $configName  The name of the configuration to fetch
     *
     * @throws \RuntimeException  If the requested config object does not exist
     *
     * @return Config  The requested configuration object
     */
    public function getConfig(string $configName = 'General'): Config
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
     * Get the route mapping table.
     *
     * @return array  The route mapping table
     */
    public function getRoutingTable(): array
    {
        return $this->routingTable;
    }

    /**
     * Get the application's base path.
     *
     * @param bool $withScriptName  Append the filename of the bootstrap script?
     *
     * @return string  The base path
     */
    public function getBasePath(bool $withScriptName = false): string
    {
        return $withScriptName ? $_SERVER['PHP_SELF'] : dirname($_SERVER['PHP_SELF']);
    }

    /**
     * Get the application's base URL.
     *
     * @param bool $withScriptName  Append the filename of the bootstrap script?
     *
     * @return string  The base URL
     */
    public function getBaseUrl(bool $withScriptName = false): string
    {
        $url = '';
        if (!$this->isCli()) {
            $url = strtolower($this->request->getProtocol()) . '://' . $this->request->getHost();
        }

        $url .= $this->getBasePath($withScriptName);

        return $url;
    }

    /**
     * Determine, whether the application was called from the command line interface.
     *
     * @return bool  Was the application called from CLI?
     */
    public function isCli(): bool
    {
        return php_sapi_name() == 'cli' || defined('STDIN');
    }
}

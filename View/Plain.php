<?php

namespace Ptf\View;

/**
 * Simple PHP only view
 */
class Plain extends Base
{
    /**
     * The base directory of the template files
     * @var string
     */
    protected $templateDir;

    /**
     * The registered function plugins
     * @var array
     */
    protected $functionPlugins;

    /**
     * Initialize the settings
     *
     * @param   \Ptf\App\Config\ViewPlain $config  The Plain view configuration
     * @param   \Ptf\App\Context $context          The application's context
     */
    public function __construct(\Ptf\App\Config\ViewPlain $config, \Ptf\App\Context $context)
    {
        parent::__construct($config, $context);

        $this->functionPlugins = [];
        $this->templateDir = $config->getTemplateDir();

        Plugin\Plain\Functions::register($this);
    }

    /**
     * Render the set template
     *
     * @param   string $cacheId             An additional cache ID, if multiple caches for the template are used
     * @throws  \RuntimeException           If no template has been set
     */
    public function render($cacheId = null)
    {
        if (!$this->templateName) {
            throw new \RuntimeException(get_class($this) . "::" . __FUNCTION__ . ": PHP template has not been set");
        }

        $tpl = $this->templateDir . '/' . $this->templateName;
        $this->context->getLogger()->logSys(__METHOD__, "Rendering template: " . $tpl);

        $oldErrorReporting = error_reporting();
        error_reporting(E_ALL & ~E_NOTICE);
        include $tpl;
        error_reporting($oldErrorReporting);
    }

    /**
     * Fetch the content of the set template as a string
     *
     * @param   string $cacheId             An additional cache ID, if multiple caches for the template are used
     * @return  string                      The fetched template
     * @throws  \RuntimeException           If no template has been set
     */
    public function fetch($cacheId = null)
    {
        if (!$this->templateName) {
            throw new \RuntimeException(get_class($this) . "::" . __FUNCTION__ . ": PHP template has not been set");
        }

        $tpl = $this->templateDir . '/' . $this->templateName;
        $this->context->getLogger()->logSys(__METHOD__, "Fetching template: " . $tpl);

        ob_start();

        $oldErrorReporting = error_reporting();
        error_reporting(E_ALL & ~E_NOTICE);
        include $tpl;
        error_reporting($oldErrorReporting);

        return ob_get_clean();
    }

    /**
     * Register a template function plugin
     *
     * @param    string $name               The name of the function plugin to register
     * @param    callable $function         The callback function for the plugin
     */
    public function registerFunctionPlugin($name, callable $function)
    {
        $this->functionPlugins[$name] = $function;
    }

    /**
     * Call a registered template function
     *
     * @param   string $name                Name of the template function to call
     * @param   array $arguments            Arguments for the template function
     * @return  mixed                       The return value of the called function
     */
    public function __call($name, array $arguments = [])
    {
        if (!count($arguments)) {
            return $this->functionPlugins[$name]($this);
        }
        return $this->functionPlugins[$name]($arguments[0], $this);
    }


    // Special template plugin functions

    /**
     * Include the given subtemplate
     *
     * @param   string $templateName        The filename of the template to include
     * @param   array $params               Parameters to be passed to the template
     */
    public function include_tpl($templateName, array $params = [])
    {
        foreach ($params as $key => $value) {
            $$key = $value;
        }

        include $this->templateDir . '/' . $templateName;

        foreach ($params as $key => $value) {
            unset($$key);
        }
    }
}

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
     * The registered plugin functions
     * @var array
     */
    protected $pluginFunctions;

    /**
     * Initialize the settings
     *
     * @param   \Ptf\App\Config\ViewPlain $config The Plain view configuration
     */
    public function __construct(\Ptf\App\Config\ViewPlain $config)
    {
        parent::__construct($config);

        $this->pluginFunctions = [];
        $this->templateDir = $config->getTemplateDir();

        \Ptf\View\Helper\PlainFunctions::register($this);
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
        include $this->templateDir . '/' . $this->templateName;
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
        ob_start();
        include $this->templateDir . '/' . $this->templateName;
        return ob_get_clean();
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
            return $this->pluginFunctions[$name]($this);
        }
        return $this->pluginFunctions[$name]($arguments[0], $this);
    }

    /**
     * Register a template function
     *
     * @param    string $name               The name of the plugin function to register
     * @param    callable $function         The callback function for the plugin
     */
    public function registerPlugin($name, callable $function)
    {
        $this->pluginFunctions[$name] = $function;
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

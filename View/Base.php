<?php

namespace Ptf\View;

use Ptf\App\Config\View as ViewConfig;
use Ptf\App\Context;

/**
 * Abstract base view class.
 */
abstract class Base implements \ArrayAccess
{
    use \Ptf\Traits\ArrayAccess;

    /** @var ViewConfig  The view's configuration */
    protected $config;

    /** @var Context  The application's context */
    protected $context;

    /** @var array  Array of assigned template variables */
    protected $assignedVars;

    /** @var string  The template to display */
    protected $templateName;

    /**
     * Initialize the member variables
     *
     * @param ViewConfig $config   The view's configuration
     * @param Context    $context  The application's context
     */
    public function __construct(ViewConfig $config, Context $context)
    {
        $this->config  = $config;
        $this->context = $context;

        $this->assignedVars = [
            'context' => $context
        ];
    }

    /**
     * Get the given template variable.<br />
     * (magic getter function).
     *
     * @param string $name  Name of the template variable to get
     *
     * @return mixed  The value of the template variable
     */
    public function __get(string $name)
    {
        return $this->assignedVars[$name] ?? null;
    }

    /**
     * Set the given template variable.<br />
     * (magic setter function).
     *
     * @param string $name   Name of the template variable to set
     * @param mixed  $value  The value to set
     */
    public function __set(string $name, $value): void
    {
        $this->assignedVars[$name] = $value;
    }

    /**
     * Determine whether the given template variabe is set.<br />
     * (magic isset function).
     *
     * @param string $name  Name of the template variable to check
     *
     * @return bool  Is the variable set?
     */
    public function __isset(string $name): bool
    {
        return isset($this->assignedVars[$name]);
    }

    /**
     * Unset the given template variable.<br />
     * (magic unset function).
     *
     * @param string $name  Name of the template variable to unset
     */
    public function __unset(string $name): void
    {
        unset($this->assignedVars[$name]);
    }

    /**
     * Set the given template variable(s).
     *
     * @param array|string $assign  Name of the template variable to set, or assoc array with multiple variables/values
     * @param mixed        $value   The value to set (if the first parameter is a string)
     */
    public function assign($assign, $value = null): void
    {
        if (is_array($assign)) {
            $this->assignedVars = array_merge($this->assignedVars, $assign);
        } else {
            $this[$assign] = $value;
        }
    }

    /**
     * Return a list of all assigned template variables.
     *
     * @return array  The assigned template variables
     */
    public function getAssignedVars(): array
    {
        return $this->assignedVars;
    }

    /**
     * Set the name of the template to be rendered.
     *
     * @param string $templateName  The name of the template to be rendered
     */
    public function setTemplateName(string $templateName): void
    {
        $this->templateName = $templateName;
    }

    /**
     * Get the name of the set template.
     *
     * @return string  The template name
     */
    public function getTemplateName(): string
    {
        return $this->templateName;
    }

    /**
     * Set the template's language.
     *
     * @param string $templateLang  The language of the template to set
     */
    public function setTemplateLanguage(string $templateLang): void
    {
        $this->tplLanguage = $templateLang;   // Set template variable (not member variable!)
    }

    /**
     * Clear the complete template cache.
     *
     * @param int $expireTime  The minimum age in seconds the cache files must have before they will get cleared
     */
    public function clearCache(int $expireTime = null): void
    {
    }

    /**
     * Determine whether the given or set template is cached.
     *
     * @param string $templateName  The name of the template to check, NULL to check the template set by setTemplateName()
     * @param string $cacheId       An additional cache ID, if multiple caches for the template are used
     *
     * @return bool  Is the template cached?
     */
    public function isCached(string $templateName = null, string $cacheId = null): bool
    {
        return false;
    }

    /**
     * Render the set template.
     *
     * @param string $cacheId  An additional cache ID, if multiple caches for the template are used
     */
    abstract public function render(string $cacheId = null): void;

    /**
     * Fetch the content of the set template as a string.
     *
     * @param string $cacheId  An additional cache ID, if multiple caches for the template are used
     *
     * @return string  The fetched template
     */
    abstract public function fetch(string $cacheId = null): string;

    /**
     * Fetch a 404 error page.
     *
     * @return string  The fetched 404 page
     */
    public function fetch404Page(): string
    {
        $tpl404 = $this->config->getTemplate404();
        if (strlen($tpl404)) {
            $this->setTemplateName($tpl404);

            return $this->fetch();
        }

        ob_start();
        include 'ErrorPages/404.phtml';

        return ob_get_clean();
    }

    /**
     * Register a template function plugin.
     *
     * @param string   $name      The name of the function plugin to register
     * @param callable $function  The callback function for the plugin
     */
    abstract public function registerFunctionPlugin(string $name, callable $function): void;
}

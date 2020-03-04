<?php

namespace Ptf\View;

use Ptf\App\Config\ViewSmarty as ViewConfig;
use Ptf\App\Context;

require_once \Ptf\BASEDIR . '/3rdparty/smarty/libs/Smarty.class.php';

/**
 * View based on Smarty 3.
 */
class Smarty extends Base
{
    /** The Smarty main directory */
    protected const SMARTY_DIR = '3rdparty/smarty/libs/';

    /** @var \Smarty_Internal_TemplateBase  The internal Smarty object */
    protected $smarty;

    /**
     * Initialize the Smarty settings.
     *
     * @param ViewConfig $config   The Smarty configuration
     * @param Context    $context  The application's context
     */
    public function __construct(ViewConfig $config, Context $context)
    {
        parent::__construct($config, $context);

        $this->smarty = new \Smarty();
        $this->smarty->setErrorReporting(E_ALL & ~E_NOTICE);

        $this->smarty->setTemplateDir($config->getTemplateDir());
        $this->smarty->setCompileDir($config->getCompileDir());
        $this->smarty->setCacheDir($config->getCacheDir());
        $this->smarty->setCaching($config->getCaching());
        $this->smarty->setCacheLifetime($config->getCacheLifetime());
        $this->smarty->setCompileCheck($config->getCompileCheck());
        $this->smarty->setForceCompile($config->getForceCompile());

        $pluginsDir = (array)$config->getPluginsDir();
        $pluginsDir[] = \Ptf\BASEDIR . '/' . self::SMARTY_DIR . 'plugins/';
        $this->smarty->setPluginsDir($pluginsDir);

        if ($config->getCompressHtml()) {
            $this->smarty->loadFilter('output', 'trimwhitespace');
        }

        Plugin\Smarty\Functions::register($this);
        Plugin\Smarty\Modifiers::register($this);
        Plugin\Smarty\Blocks::register($this);

        $this->smarty->assign('context', $context);
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
        return $this->smarty->getTemplateVars($name);
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
        $this->smarty->assign($name, $value);
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
        return $this->smarty->getTemplateVars($name) !== null;
    }

    /**
     * Unset the given template variable.<br />
     * (magic unset function).
     *
     * @param string $name  Name of the template variable to unset
     */
    public function __unset(string $name): void
    {
        $this->smarty->clearAssign($name);
    }

    /**
     * Set the given template variable(s).
     *
     * @param array|string $assign  Name of the template variable to set, or assoc array with multiple variables/values
     * @param mixed        $value   The value to set (if the first parameter is a string)
     */
    public function assign($assign, $value = null): void
    {
        $this->smarty->assign($assign, $value);
    }

    /**
     * Return a list of all assigned template variables.
     *
     * @return array  The assigned template variables
     */
    public function getAssignedVars(): array
    {
        return $this->smarty->getTemplateVars();
    }

    /**
     * Get the internal Smarty object.
     *
     * @return \Smarty_Internal_TemplateBase  The internal Smarty object
     */
    public function getSmartyObject(): \Smarty_Internal_TemplateBase
    {
        return $this->smarty;
    }

    /**
     * Set the internal Smarty object.<br />
     * This function is only for internal framework purposes!
     *
     * @see \Ptf\View\Plugin\Smarty\Functions::exec()
     * @ignore
     *
     * @param \Smarty_Internal_TemplateBase $smarty  The Smarty object to set
     */
    public function _setSmartyObject(\Smarty_Internal_TemplateBase $smarty): void
    {
        $this->smarty = $smarty;
    }

    /**
     * Clear the complete cache and the compiled templates dir.
     */
    public function clearAll(): void
    {
        $this->smarty->clearAllCache();
        $this->smarty->clearCompiledTemplate();
    }

    /**
     * Clear the complete template cache.
     *
     * @param int $expireTime  The minimum age in seconds the cache files must be before they will get cleared
     */
    public function clearCache(int $expireTime = null): void
    {
        $this->smarty->clearAllCache($expireTime);
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
        if ($templateName === null) {
            $templateName = $this->templateName;
        }

        if ($this->cacheId !== null) {
            $cacheId = $this->cacheId;
        }

        return $this->smarty->isCached($templateName, $cacheId);
    }

    /**
     * Render the set template.
     *
     * @param string $cacheId  An additional cache ID, if multiple caches for the template are used
     *
     * @throws \RuntimeException  If no template has been set
     */
    public function render(string $cacheId = null): void
    {
        if (!$this->templateName) {
            throw new \RuntimeException(get_class($this) . '::' . __FUNCTION__ . ': Smarty template has not been set');
        }

        $this->context->getLogger()->logSys(__METHOD__, 'Rendering template: '
            . $this->config->getTemplateDir() . '/' . $this->templateName);

        if ($this->cacheId !== null) {
            $cacheId = $this->cacheId;
        }

        $this->smarty->display($this->templateName, $cacheId);
    }

    /**
     * Fetch the content of the set template as a string.
     *
     * @param string $cacheId  An additional cache ID, if multiple caches for the template are used
     *
     * @throws \RuntimeException  If no template has been set
     *
     * @return string  The fetched template
     */
    public function fetch(string $cacheId = null): string
    {
        if (!$this->templateName) {
            throw new \RuntimeException(get_class($this) . '::' . __FUNCTION__ . ': Smarty template has not been set');
        }

        $this->context->getLogger()->logSys(__METHOD__, 'Fetching template: '
            . $this->config->getTemplateDir() . '/' . $this->templateName);

        if ($this->cacheId !== null) {
            $cacheId = $this->cacheId;
        }

        return $this->smarty->fetch($this->templateName, $cacheId);
    }

    /**
     * Register a template function plugin.
     *
     * @param string   $name      The name of the function plugin to register
     * @param callable $function  The callback function for the plugin
     */
    public function registerFunctionPlugin(string $name, callable $function): void
    {
        $this->smarty->registerPlugin('function', $name, $function);
    }

    /**
     * Register a template block plugin.
     *
     * @param string   $name      The name of the block plugin to register
     * @param callable $function  The callback function for the plugin
     */
    public function registerBlockPlugin(string $name, callable $function): void
    {
        $this->smarty->registerPlugin('block', $name, $function);
    }

    /**
     * Register a template modifier plugin.
     *
     * @param string   $name      The name of the modifier plugin to register
     * @param callable $function  The callback function for the plugin
     */
    public function registerModifierPlugin(string $name, callable $function): void
    {
        $this->smarty->registerPlugin('modifier', $name, $function);
    }
}

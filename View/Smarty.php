<?php

namespace Ptf\View;

require_once \Ptf\BASEDIR . '/3rdparty/smarty/libs/Smarty.class.php';

/**
 * View based on Smarty 3
 */
class Smarty extends Base
{
    /** The Smarty main directory */
    const SMARTY_DIR = '3rdparty/smarty/libs/';

    /**
     * The internal Smarty object
     * @var \Smarty
     */
    protected $smarty;

    /**
     * Initialize the Smarty settings
     *
     * @param   \Ptf\App\Config\ViewSmarty $config The Smarty configuration
     */
    public function __construct(\Ptf\App\Config\ViewSmarty $config)
    {
        parent::__construct($config);

        $this->assignedVars = [];
        $this->smarty = new \Smarty();

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

        \Ptf\View\Helper\SmartyFunctions::register($this->smarty);
        \Ptf\View\Helper\SmartyModifiers::register($this->smarty);
        \Ptf\View\Helper\SmartyBlocks::register($this->smarty);
    }

    /**
     * Set the given template variable.<br />
     * (magic setter function)
     *
     * @param   string $name                Name of the template variable to set
     * @param   mixed $value                The value to set
     */
    public function __set($name, $value)
    {
        parent::__set($name, $value);
        $this->smarty->assign($name, $value);
    }

    /**
     * Unset the given template variable.<br />
     * (magic unset function)
     *
     * @param   string $name                The template variable to unset
     */
    public function __unset($name)
    {
        parent::__unset($name);
        $this->smarty->clearAssign($name);
    }

    /**
     * Clear the complete cache and the compiled templates dir
     */
    public function clearAll()
    {
        $this->smarty->clearAllCache();
        $this->smarty->clearCompiledTemplate();
    }

    /**
     * Clear the complete template cache
     *
     * @param   integer $expireTime         The minimum age in seconds the cache files must be before they will get cleared
     */
    public function clearCache($expireTime = null)
    {
        $this->smarty->clearAllCache($expireTime);
    }

    /**
     * Determine whether the given or set template is cached
     *
     * @param   string $templateName       The name of the template to check, NULL to check the template set by setTemplateName()
     * @param   string $cacheId            An additional cache ID, if multiple caches for the template are used
     * @return  boolean                    Is the template cached?
     */
    public function isCached($templateName = null, $cacheId = null)
    {
        if ($templateName === null) {
            $templateName = $this->templateName;
        }
        return $this->smarty->isCached($templateName, $cacheId);
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
            throw new \RuntimeException(get_class($this) . "::" . __FUNCTION__ . ": Smarty template has not been set");
        }
        $this->smarty->display($this->templateName, $cacheId);
    }

    /**
     * Fetch the content of the set template as a string
     *
     * @param   string $cacheId             An additional cache ID, if multiple caches for the template are used
     * @throws  \RuntimeException           If no template has been set
     */
    public function fetch($cacheId = null)
    {
        if (!$this->templateName) {
            throw new \RuntimeException(get_class($this) . "::" . __FUNCTION__ . ": Smarty template has not been set");
        }
        return $this->smarty->fetch($this->templateName, $cacheId);
    }

}

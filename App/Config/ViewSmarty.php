<?php

namespace Ptf\App\Config;

/**
 * Configuration for the Smarty view
 */
class ViewSmarty extends \Ptf\App\Config\View
{
    /**
     * Initialize the configuration data
     */
    public function __construct()
    {
        parent::__construct();

        $this->configData = array_merge($this->configData, [
            'compile_dir'    => null,
            'cache_dir'      => '',
            'caching'        => '0',
            'cache_lifetime' => '0',
            'plugins_dir'    => '',
            'compress_html'  => '1',
            'compile_check'  => '0',
            'force_compile'  => '0'
        ]);
    }

    /**
     * Get the compile directory setting
     *
     * @return  string                      The configured compile directory
     */
    public function getCompileDir()
    {
        return $this->compile_dir;
    }

    /**
     * Get the cache directory setting
     *
     * @return  string                      The configured cache directory
     */
    public function getCacheDir()
    {
        return $this->cache_dir;
    }

    /**
     * Get the caching setting
     *
     * @return  string                      The configured caching setting
     */
    public function getCaching()
    {
        return $this->caching;
    }

    /**
     * Get the cache lifetime setting
     *
     * @return  string                      The configured cache lifetime [sec]
     */
    public function getCacheLifetime()
    {
        return $this->cache_lifetime;
    }

    /**
     * Get the plugins directory setting
     *
     * @return  string|string[]             The configured plugins path(s)
     */
    public function getPluginsDir()
    {
        return $this->plugins_dir;
    }

    /**
     * Get the "compress_html" setting
     *
     * @return  string                      The configured "compress_html" setting
     */
    public function getCompressHtml()
    {
        return $this->compress_html;
    }

    /**
     * Get the "compile_check" setting
     *
     * @return  string                      The configured "compile_check" setting
     */
    public function getCompileCheck()
    {
        return $this->compile_check;
    }

    /**
     * Get the "force_compile" setting
     *
     * @return  string                      The configured "force_compile" setting
     */
    public function getForceCompile()
    {
        return $this->force_compile;
    }

}

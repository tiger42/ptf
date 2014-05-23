<?php

namespace Ptf\App\Config;

/**
 * Configuration for View classes
 */
class View extends \Ptf\App\Config
{
    /**
     * Initialize the configuration data
     */
    public function __construct()
    {
        parent::__construct();

        $this->configData = array_merge($this->configData, [
            'template_dir' => null,
            'template_404' => ''
        ]);
    }

    /**
     * Get the template directory setting
     *
     * @return  string                      The configured template directory
     */
    public function getTemplateDir()
    {
        return $this->template_dir;
    }

    /**
     * Get the name of the "404 Not Found" template
     *
     * @return  string                      The configured 404 template name
     */
    public function getTemplate404()
    {
        return $this->template_404;
    }

}

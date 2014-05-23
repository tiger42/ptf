<?php

namespace Ptf\App\Config;

/**
 * Configuration for Session classes
 */
class Session extends \Ptf\App\Config
{
    /**
     * Initialize the configuration data
     */
    public function __construct()
    {
        parent::__construct();

        $this->configData = array_merge($this->configData, [
            'session_name' => '',
            'max_lifetime' => ''
        ]);
    }

    /**
     * Get the name of the session
     *
     * @return  string                      The configured session name
     */
    public function getSessionName()
    {
        return $this->session_name;
    }

    /**
     * Get the maximum session lifetime
     *
     * @return  string                      The configured maximum session lifetime [sec]
     */
    public function getMaxLifetime()
    {
        return $this->max_lifetime;
    }

}

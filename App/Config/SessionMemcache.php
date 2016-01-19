<?php

namespace Ptf\App\Config;

/**
 * Configuration for Memcache session
 */
class SessionMemcache extends \Ptf\App\Config\Session
{
    /**
     * Initialize the configuration data
     */
    public function __construct()
    {
        parent::__construct();

        $this->configData = array_merge($this->configData, [
            'hosts' => [
                'localhost:11211',
            ]
        ]);
    }

    /**
     * Get the memcache hosts setting
     *
     * @return  array                       The configured memcache hosts
     */
    public function getHosts()
    {
        return $this->hosts;
    }
}

<?php

namespace Ptf\App\Config;

/**
 * Configuration for Auth classes
 */
class Auth extends \Ptf\App\Config
{
    /**
     * Initialize the configuration data
     */
    public function __construct()
    {
        $this->configData = [
            'idletime' => '1800',   // 30 min
            'salt'     => 'mysecretsalt'
        ];
    }

    /**
     * Get the idletime setting
     *
     * @return  string                      The configured idletime [sec]
     */
    public function getIdletime()
    {
        return $this->idletime;
    }

    /**
     * Get the checksum salt
     *
     * @return   string                     The checksum salt
     */
    public function getSalt()
    {
        return $this->salt;
    }
}

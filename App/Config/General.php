<?php

namespace Ptf\App\Config;

/**
 * General application configuration
 */
class General extends \Ptf\App\Config
{
    /**
     * Initialize the configuration data
     */
    public function __construct()
    {
        $this->configData = [
            'log_level'  => (string)\Ptf\Util\Logger::INFO,
            'system_log' => 'var/log/system.log',
            'error_log'  => 'var/log/error.log'
        ];
    }

    /**
     * Get the log level setting
     *
     * @return  string                      The configured log level
     */
    public function getLogLevel()
    {
        return $this->log_level;
    }

    /**
     * Get the system log filename
     *
     * @return  string                      The configured system log
     */
    public function getSystemLog()
    {
        return $this->system_log;
    }

    /**
     * Get the error log filename
     *
     * @return  string                      The configured error log
     */
    public function getErrorLog()
    {
        return $this->error_log;
    }
}

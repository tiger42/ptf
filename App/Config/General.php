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
            'log_level' => (string)\Ptf\Util\Logger::INFO
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
}

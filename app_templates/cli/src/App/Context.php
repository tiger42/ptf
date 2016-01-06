<?php

namespace _NAMESPACE_\App;

/**
 * The application's context
 */
class Context extends \Ptf\App\Context
{
    /**
     * Initialize the Logger objects
     */
    protected function initLoggers()
    {
        $logLevelLimit = (int)$this->getConfig('General')->getLogLevel();
        $this->loggers = [
            'system' => \Ptf\Util\Logger\File::getInstance(\_NAMESPACE_\APPDIR . '/var/log/system.log', $this, $logLevelLimit),
            'error'  => \Ptf\Util\Logger\File::getInstance(\_NAMESPACE_\APPDIR . '/var/log/error.log', $this, $logLevelLimit)
        ];
    }

    /**
     * Get the application's namespace
     *
     * @return  string                      The namespace of the application
     */
    public function getAppNamespace()
    {
        return '_NAMESPACE_';
    }

    /**
     * Get the name of the default controller
     *
     * @return  string                      The name of the default controller
     */
    public function getDefaultControllerName()
    {
        return 'Task';
    }

}

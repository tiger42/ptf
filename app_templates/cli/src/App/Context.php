<?php

namespace _NAMESPACE_\App;

use Ptf\Util\Logger\File as FileLogger;

/**
 * The application's context.
 */
class Context extends \Ptf\App\Context
{
    /**
     * Initialize the Logger objects.
     */
    protected function initLoggers(): void
    {
        $logLevelLimit = (int)$this->getConfig('General')->getLogLevel();
        $this->loggers = [
            'system' => FileLogger::getInstance(\_NAMESPACE_\APPDIR . '/var/log/system.log', $this, $logLevelLimit),
            'error'  => FileLogger::getInstance(\_NAMESPACE_\APPDIR . '/var/log/error.log', $this, $logLevelLimit)
        ];
    }

    /**
     * Get the application's namespace.
     *
     * @return string  The namespace of the application
     */
    public function getAppNamespace(): string
    {
        return '_NAMESPACE_';
    }

    /**
     * Get the name of the default controller.
     *
     * @return string  The name of the default controller
     */
    public function getDefaultControllerName(): string
    {
        return 'Task';
    }
}

<?php

namespace Ptf\Util\Logger;

/**
 * Dummy logger
 */
class DevNull extends \Ptf\Util\Logger
{
    /**
     * Open the log file
     */
    protected function openLog()
    {
    }

    /**
     * Close the log file
     */
    protected function closeLog()
    {
    }

    /**
     * Add a line to the log file
     *
     * @param   string $message             The message to log
     * @param   integer $logLevel           The log level of the message
     * @param   string $timestamp           Timestamp of the message
     * @param   string $remoteAddress       IP address of the client
     */
    protected function logImpl($message, $logLevel, $timestamp, $remoteAddress)
    {
    }

}

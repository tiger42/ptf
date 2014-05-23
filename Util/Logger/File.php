<?php

namespace Ptf\Util\Logger;

/**
 * Logger for logging into a file
 */
class File extends \Ptf\Util\Logger
{
    /**
     * File handle of log file
     * @var resource
     */
    protected $logFile;

    /**
     * Open the log file
     *
     * @throws  \Ptf\Core\Exception\Logger  If the log file could not be opened
     */
    protected function openLog()
    {
        if (!is_resource($this->logFile)) {
            $this->logFile = @fopen($this->logName, 'ab');
            if (!is_resource($this->logFile)) {
                throw new \Ptf\Core\Exception\Logger(get_class($this) . "::" . __FUNCTION__ . ": Error opening file '" . $this->logName . "'");
            }
            flock($this->logFile, LOCK_EX | LOCK_NB);
        }
    }

    /**
     * Close the log file
     */
    protected function closeLog()
    {
        if (is_resource($this->logFile)) {
            flock($this->logFile, LOCK_UN);
            fclose($this->logFile);
        }
    }

    /**
     * Add a line to the log file
     *
     * @param   string $message             The message to log
     * @param   integer $logLevel           The log level of the message
     * @param   string $timestamp           Timestamp of the message
     * @param   string $remoteAddress       IP address of the client
     * @throws  \Ptf\Core\Exception\Logger  If the log message could not be written
     */
    protected function logImpl($message, $logLevel, $timestamp, $remoteAddress)
    {
        $line = sprintf("[%s][%-15s][%-5s] %s\n", $timestamp, $remoteAddress, $this->translateLogLevel($logLevel), $message);
        if (!fwrite($this->logFile, $line)) {
            throw new \Ptf\Core\Exception\Logger(get_class($this) . "::" . __FUNCTION__ . ": Unable to write to log file");
        }
        fflush($this->logFile);
    }

}

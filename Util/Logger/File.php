<?php

namespace Ptf\Util\Logger;

use Ptf\Core\Exception\Logger as LoggerException;

/**
 * Logger for logging into a file.
 */
class File extends \Ptf\Util\Logger
{
    /**
     * File handle of log file
     * @var resource
     */
    protected $logFile;

    /**
     * Open the log file.
     *
     * @throws LoggerException  If the log file could not be opened
     */
    protected function openLog(): void
    {
        if (!is_resource($this->logFile)) {
            $this->logFile = @fopen($this->logName, 'ab');
            if (!is_resource($this->logFile)) {
                throw new LoggerException(get_class($this) . '::' . __FUNCTION__
                    . ': Error opening file "' . $this->logName . '"');
            }
            flock($this->logFile, LOCK_EX | LOCK_NB);
        }
    }

    /**
     * Close the log file.
     */
    protected function closeLog(): void
    {
        if (is_resource($this->logFile)) {
            flock($this->logFile, LOCK_UN);
            fclose($this->logFile);
        }
    }

    /**
     * Add a line to the log file.
     *
     * @param string $message        The message to log
     * @param int    $logLevel       The log level of the message
     * @param string $timestamp      Timestamp of the message
     * @param string $remoteAddress  IP address of the client
     *
     * @throws LoggerException  If the log message could not be written
     */
    protected function logImpl(string $message, int $logLevel, string $timestamp, string $remoteAddress): void
    {
        $line = sprintf("[%s][%-15s][%-5s] %s\n", $timestamp, $remoteAddress, $this->translateLogLevel($logLevel), $message);
        if (!fwrite($this->logFile, $line)) {
            throw new LoggerException(get_class($this) . '::' . __FUNCTION__ . ': Unable to write to log file');
        }
        fflush($this->logFile);
    }
}

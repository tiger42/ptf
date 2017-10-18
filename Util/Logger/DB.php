<?php

namespace Ptf\Util\Logger;

use Ptf\App\Config\DB as DBConfig;
use Ptf\Core\Exception\Logger as LoggerException;
use Ptf\Model\DB\Table as DBTable;

/**
 * Logger for logging into a database table.
 *
 * <pre>
 * Database table to be created for the logger (MySQL syntax):
 *
 * CREATE TABLE {LogName} (
 *     id int(11) NOT NULL AUTO_INCREMENT,
 *     log_time datetime DEFAULT NULL,
 *     remote_addr varchar(15) DEFAULT NULL,
 *     log_level varchar(5) DEFAULT NULL,
 *     message text,
 *     PRIMARY KEY (id)
 * );
 * </pre>
 */
class DB extends \Ptf\Util\Logger
{
    /**
     * Database table object for log table
     * @var DBTable
     */
    protected $logTable;

    /**
     * DB configuration object
     * @var DBConfig
     */
    protected $dbConfig;

    /**
     * Connect to the database to log into.
     *
     * @throws LoggerException  If the database is not accessible
     */
    protected function openLog(): void
    {
        if (!$this->logTable) {
            if (!$this->dbConfig) {
                throw new LoggerException(get_class($this) . "::" . __FUNCTION__
                    . ": DB configuration has not been set");
            }
            try {
                $this->logTable = new DBTable($this->logName, $this->dbConfig, $this->context);
            } catch (\Ptf\Exception\DBConnect $e) {
                throw new LoggerException(get_class($this) . "::" . __FUNCTION__ . ": " . $e->getMessage());
            }
        }
    }

    /**
     * Close connection to the database.
     */
    protected function closeLog(): void
    {
        unset($this->logTable);
    }

    /**
     * Add a line to the log table.
     *
     * @param string $message        The message to log
     * @param int    $logLevel       The log level of the message
     * @param string $timestamp      Timestamp of the message
     * @param string $remoteAddress  IP address of the client
     *
     * @throws LoggerException  If the log message could not be inserted
     */
    protected function logImpl(string $message, int $logLevel, string $timestamp, string $remoteAddress): void
    {
        $this->logTable->log_time    = $timestamp;
        $this->logTable->remote_addr = $remoteAddress;
        $this->logTable->log_level   = $this->translateLogLevel($logLevel);
        $this->logTable->message     = $message;

        try {
            $this->logTable->insert();
        } catch (\Ptf\Exception\DBQuery $e) {
            throw new \Ptf\Core\Exception\Logger(get_class($this) . "::" . __FUNCTION__ . ": " . $e->getMessage());
        }
    }

    /**
     * Set the DB configuration object.
     *
     * @param DBConfig $config  The DB configuration to set
     */
    public function setDBConfig(DBConfig $config): void
    {
        $this->dbConfig = $config;
    }
}

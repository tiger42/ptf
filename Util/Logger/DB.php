<?php

namespace Ptf\Util\Logger;

/**
 * Logger for logging into a database table
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
     * @var \Ptf\Model\DB\Table
     */
    protected $logTable;
    /**
     * DB configuration object
     * @var \Ptf\App\Config\DB
     */
    protected $dbConfig;

    /**
     * Connect to the database to log into
     *
     * @throws  \Ptf\Core\Exception\Logger  If the database is not accessible
     */
    protected function openLog()
    {
        if (!$this->logTable) {
            if (!$this->dbConfig) {
                throw new \Ptf\Core\Exception\Logger(get_class($this) . "::" . __FUNCTION__ . ": DB configuration has not been set");
            }
            try {
                $this->logTable = new \Ptf\Model\DB\Table($this->logName, $this->dbConfig, $this->context);
            } catch (\Ptf\Exception\DBConnect $e) {
                throw new \Ptf\Core\Exception\Logger(get_class($this) . "::" . __FUNCTION__ . ": " . $e->getMessage());
            }
        }
    }

    /**
     * Close connection to the database
     */
    protected function closeLog()
    {
        unset($this->logTable);
    }

    /**
     * Add a line to the log table
     *
     * @param   string $message             The message to log
     * @param   integer $logLevel           The log level of the message
     * @param   string $timestamp           Timestamp of the message
     * @param   string $remoteAddress       IP address of the client
     * @throws  \Ptf\CoreException\Logger   If the log message could not be inserted
     */
    protected function logImpl($message, $logLevel, $timestamp, $remoteAddress)
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
     * Set the DB configuration object
     *
     * @param   \Ptf\App\Config\DB $config  The DB configuration to set
     */
    public function setDBConfig(\Ptf\App\Config\DB $config)
    {
        $this->dbConfig = $config;
    }

}

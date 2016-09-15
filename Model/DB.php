<?php

namespace Ptf\Model;

/**
 * Abstract database wrapper
 */
abstract class DB
{
    /**
     * Singleton instances of concrete DB classes
     * @var \Ptf\Model\DB[]
     */
    private static $instances = [];

    /**
     * ID of the current instance
     * @var string
     */
    protected $instanceId;

    /**
     * The system logger
     * @var \Ptf\Util\Logger
     */
    protected $logger;

    /**
     * The error logger
     * @var \Ptf\Util\Logger
     */
    protected $errLogger;

    /**
     * The current configuration data
     * @var \Ptf\App\Config\DB
     */
    protected $config;

    /**
     * The application's context
     * @var \Ptf\App\Context
     */
    protected $context;

    /**
     * Cache for column names per table
     * @var array
     */
    protected $columnNames;

    /**
     * Initialize the member variables and connect to the database
     *
     * @param   \Ptf\App\Config\DB $config  The DB configuration
     * @param   \Ptf\App\Context $context   The application's context
     * @param   string $id                  The instance ID
     */
    protected function __construct(\Ptf\App\Config\DB $config, \Ptf\App\Context $context, $id = '')
    {
        $this->config      = $config;
        $this->context     = $context;
        $this->instanceId  = $id;
        $this->columnNames = [];

        $this->logger    = $context->getLogger('system');
        $this->errLogger = $context->getLogger('error');

        $this->connect();
    }

    /**
     * Disconnect from the database
     */
    abstract public function __destruct();

    /**
     * Prevent all child classes' Singleton instances from being cloned
     */
    final private function __clone()
    {
    }

    /**
     * Prevent all child classes' Singleton instances from being unserialized
     */
    final private function __wakeup()
    {
    }

    /**
     * Get Singleton instance of DB, depending on the given config object
     *
     * @param   \Ptf\App\Config\DB $config  The DB configuration
     * @param   \Ptf\App\Context $context   The application's context
     * @param   string $id                  Optional ID to get different instances for same config
     * @return  \Ptf\Model\DB               Singleton instance of concrete DB class
     */
    final public static function getInstance(\Ptf\App\Config\DB $config, \Ptf\App\Context $context, $id = '')
    {
        $key = md5(serialize($config) . $id);

        if (!isset(self::$instances[$key])) {
            $className = '\\Ptf\\Model\\DB\\' . str_replace('_', '\\', $config->getDriver());
            self::$instances[$key] = new $className($config, $context, $id);
        }
        return self::$instances[$key];
    }

    /**
     * Get the current configuration
     *
     * @return  \Ptf\App\Config\DB          The current configuration
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * Connect to the database
     */
    abstract protected function connect();

    /**
     * Perform a "SELECT" query on the database
     *
     * @param   string $query               The SQL query string
     * @param   integer $offset             Offset of the first row
     * @param   integer $rowCount           Number of rows to fetch
     * @return  integer                     The number of fetched rows
     */
    final public function query($query, $offset = 0, $rowCount = null)
    {
        $msg = $query . ($offset > 0 || $rowCount !== null ? "; Offset: " . $offset . ", Count: " . $rowCount : "");
        $this->logger->logSys(get_class($this) . "::" . __FUNCTION__, $msg);

        return $this->queryImpl($query, $offset, $rowCount);
    }

    /**
     * Perform a "SELECT" query on the database.<br />
     * (to be implemented by child classes)
     *
     * @param   string $query               The SQL query string
     * @param   integer $offset             Offset of the first row
     * @param   integer $rowCount           Number of rows to fetch
     * @return  integer                     The number of fetched rows
     */
    abstract protected function queryImpl($query, $offset = 0, $rowCount = null);

    /**
     * Fetch a row from the query result, advance the row pointer
     *
     * @return  mixed                       Result row as assoc array; FALSE, if result has no more rows
     */
    abstract public function fetch();

    /**
     * Perform a manipulation query on the database (e.g. "UPDATE", "INSERT")
     *
     * @param   string $sql                 The SQL statement to execute
     */
    final public function execSql($sql)
    {
        $this->logger->logSys(get_class($this) . "::" . __FUNCTION__, $sql);

        $this->execSqlImpl($sql);
    }

    /**
     * Perform a manipulation query on the database (e.g. "UPDATE", "INSERT").<br />
     * (to be implemented by child classes)
     *
     * @param   string $sql                 The SQL statement to execute
     */
    abstract protected function execSqlImpl($sql);

    /**
     * Return the number of fetched rows of the last "SELECT" statement
     *
     * @return  integer                     The number of fetched rows
     */
    final public function getFetchedRowsCount()
    {
        $count = $this->getFetchedRowsCountImpl();

        $this->logger->logSys(get_class($this) . "::" . __FUNCTION__, $count);

        return $count;
    }

    /**
     * Return the number of fetched rows of the last "SELECT" statement.<br />
     * (to be implemented by child classes)
     *
     * @return  integer                     The number of fetched rows
     */
    abstract protected function getFetchedRowsCountImpl();

    /**
     * Return the number of affected rows after the last "INSERT", "UPDATE" or "DELETE" statement
     *
     * @return  integer                     The number of affected rows
     */
    final public function getAffectedRowsCount()
    {
        $count = $this->getAffectedRowsCountImpl();

        $this->logger->logSys(get_class($this) . "::" . __FUNCTION__, $count);

        return $count;
    }

    /**
     * Return the number of affected rows after the last "INSERT", "UPDATE" or "DELETE" statement.<br />
     * (to be implemented by child classes)
     *
     * @return  integer                     The number of affected rows
     */
    abstract protected function getAffectedRowsCountImpl();

    /**
     * Return the last insert ID after an "INSERT" statement (works only for tables with autoincrement key!)
     *
     * @return  integer                     The last insert ID
     */
    final public function getLastInsertId()
    {
        $id = $this->getLastInsertIdImpl();

        $this->logger->logSys(get_class($this) . "::" . __FUNCTION__, $id);

        return $id;
    }

    /**
     * Return the last insert ID after an "INSERT" statement (works only for tables with autoincrement key!).<br />
     * (to be implemented by child classes)
     *
     * @return  integer                     The last insert ID
     */
    abstract protected function getLastInsertIdImpl();

    /**
     * Return all column names of the given table
     *
     * @param   string $tableName           Name of the table to determine the column names of
     * @return  string[]                    The names of the table's columns
     */
    final public function getColumnNames($tableName)
    {
        if (!isset($this->columnNames[$tableName])) {
            $this->columnNames[$tableName] = $this->getColumnNamesImpl($tableName);
        }
        return $this->columnNames[$tableName];
    }

    /**
     * Return all column names of the given table.<br />
     * (to be implemented by child classes)
     *
     * @param   string $tableName           Name of the table to determine the column names of
     * @return  string[]                    The names of the table's columns
     */
    abstract protected function getColumnNamesImpl($tableName);

    /**
     * Start a transaction
     *
     * @return  boolean                     Was the operation successful?
     */
    final public function startTransaction()
    {
        $res = $this->startTransactionImpl();

        if ($res) {
            $this->logger->logSys(get_class($this) . "::" . __FUNCTION__, "Transaction successfully started");
        } else {
            $this->errLogger->logSys(
                get_class($this) . "::" . __FUNCTION__,
                "Transaction could not be started",
                \Ptf\Util\Logger::WARN
            );
        }
        return $res;
    }

    /**
     * Start a transaction
     * (to be implemented by child classes)
     *
     * @return  boolean                     Was the operation succesful?
     */
    abstract protected function startTransactionImpl();

    /**
     * Commit the current transaction
     *
     * @return  boolean                     Was the operation successful?
     */
    final public function commitTransaction()
    {
        $res = $this->commitTransactionImpl();

        if ($res) {
            $this->logger->logSys(get_class($this) . "::" . __FUNCTION__, "Transaction successfully commited");
        } else {
            $this->errLogger->logSys(
                get_class($this) . "::" . __FUNCTION__,
                "Transaction could not be commited",
                \Ptf\Util\Logger::WARN
            );
        }
        return $res;
    }

    /**
     * Commit the current transaction
     * (to be implemented by child classes)
     *
     * @return  boolean                     Was the operation successful?
     */
    abstract protected function commitTransactionImpl();

    /**
     * Roll back the current transaction
     *
     * @return  boolean                     Was the operation successful?
     */
    final public function rollbackTransaction()
    {
        $res = $this->rollbackTransactionImpl();

        if ($res) {
            $this->logger->logSys(get_class($this) . "::" . __FUNCTION__, "Transaction successfully rollbacked");
        } else {
            $this->errLogger->logSys(
                get_class($this) . "::" . __FUNCTION__,
                "Transaction could not be rollbacked",
                \Ptf\Util\Logger::WARN
            );
        }
        return $res;
    }

    /**
     * Roll back the current transaction
     * (to be implemented by child classes)
     *
     * @return  boolean                     Was the operation successful?
     */
    abstract protected function rollbackTransactionImpl();

    /**
     * Create a Table object for the table with the given name
     *
     * @param   string $tableName           Name of the database table
     * @param   string $orderBy             The column to order by
     * @param   string $orderDir            The direction to order by ('ASC' or 'DESC')
     * @return  \Ptf\Model\DB\Table         The created table object
     */
    public function createTableObj($tableName, $orderBy = null, $orderDir = 'ASC')
    {
        $dbTable = new \Ptf\Model\DB\Table($tableName, $this->config, $orderBy, $orderDir, $this->instanceId);

        return $dbTable;
    }

    /**
     * Quote the given identifier (e.g. table or column name)
     *
     * @param   string $string              The string to be quoted
     * @return  string                      The quoted string
     */
    abstract public function quoteIdentifier($string);

    /**
     * Escape a string to be safely used in database queries
     *
     * @param   string $string              The string to be escaped
     * @return  string                      The escaped string
     */
    abstract public function escapeString($string);

    /**
     * Unescape a string
     *
     * @param   string $string              The string to be unescaped
     * @return  string                      The unescaped string
     */
    abstract public function unEscapeString($string);
}

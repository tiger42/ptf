<?php

namespace Ptf\Model;

use Ptf\App\Context;
use Ptf\App\Config\DB as DBConfig;
use Ptf\Model\DB\Table;
use Ptf\Util\Logger;

/**
 * Abstract database wrapper.
 */
abstract class DB
{
    /**
     * Singleton instances of concrete DB classes
     * @var DB[]
     */
    private static $instances = [];

    /**
     * ID of the current instance
     * @var string
     */
    protected $instanceId;

    /**
     * The system logger
     * @var Logger
     */
    protected $logger;

    /**
     * The error logger
     * @var Logger
     */
    protected $errLogger;

    /**
     * The current configuration data
     * @var DBConfig
     */
    protected $config;

    /**
     * The application's context
     * @var Context
     */
    protected $context;

    /**
     * Cache for column names per table
     * @var array
     */
    protected $columnNames;

    /**
     * Initialize the member variables and connect to the database.
     *
     * @param DBConfig $config   The DB configuration
     * @param Context  $context  The application's context
     * @param string   $id       The instance ID
     */
    protected function __construct(DBConfig $config, Context $context, string $id = '')
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
     * Disconnect from the database.
     */
    abstract public function __destruct();

    /**
     * Prevent all child classes' Singleton instances from being cloned.
     */
    final private function __clone()
    {
    }

    /**
     * Prevent all child classes' Singleton instances from being unserialized.
     */
    final private function __wakeup()
    {
    }

    /**
     * Get Singleton instance of DB, depending on the given config object.
     *
     * @param DBConfig $config   The DB configuration
     * @param Context  $context  The application's context
     * @param string   $id       Optional ID to get different instances for same config
     *
     * @return \Ptf\Model\DB  Singleton instance of concrete DB class
     */
    final public static function getInstance(DBConfig $config, Context $context, string $id = ''): DB
    {
        $key = md5(serialize($config) . $id);

        if (!isset(self::$instances[$key])) {
            $className = '\\Ptf\\Model\\DB\\' . str_replace('_', '\\', $config->getDriver());
            self::$instances[$key] = new $className($config, $context, $id);
        }
        return self::$instances[$key];
    }

    /**
     * Get the current configuration.
     *
     * @return DBConfig  The current configuration
     */
    public function getConfig(): DBConfig
    {
        return $this->config;
    }

    /**
     * Connect to the database.
     */
    abstract protected function connect(): void;

    /**
     * Perform a "SELECT" query on the database.
     *
     * @param string $query     The SQL query string
     * @param int    $offset    Offset of the first row
     * @param int    $rowCount  Number of rows to fetch
     *
     * @return int  The number of fetched rows
     */
    final public function query(string $query, int $offset = 0, int $rowCount = null): int
    {
        $msg = $query . ($offset > 0 || $rowCount !== null ? "; Offset: " . $offset . ", Count: " . $rowCount : "");
        $this->logger->logSys(get_class($this) . "::" . __FUNCTION__, $msg);

        return $this->queryImpl($query, $offset, $rowCount);
    }

    /**
     * Perform a "SELECT" query on the database.<br />
     * (to be implemented by child classes).
     *
     * @param string $query     The SQL query string
     * @param int    $offset    Offset of the first row
     * @param int    $rowCount  Number of rows to fetch
     *
     * @return  int  The number of fetched rows
     */
    abstract protected function queryImpl(string $query, int $offset = 0, int $rowCount = null);

    /**
     * Fetch a row from the query result, advance the row pointer.
     *
     * @return array|false  Result row as assoc array; FALSE, if result has no more rows
     */
    abstract public function fetch();

    /**
     * Perform a manipulation query on the database (e.g. "UPDATE", "INSERT").
     *
     * @param string $sql  The SQL statement to execute
     */
    final public function execSql(string $sql): void
    {
        $this->logger->logSys(get_class($this) . "::" . __FUNCTION__, $sql);

        $this->execSqlImpl($sql);
    }

    /**
     * Perform a manipulation query on the database (e.g. "UPDATE", "INSERT").<br />
     * (to be implemented by child classes).
     *
     * @param string $sql  The SQL statement to execute
     */
    abstract protected function execSqlImpl(string $sql): void;

    /**
     * Return the number of fetched rows of the last "SELECT" statement.
     *
     * @return int  The number of fetched rows
     */
    final public function getFetchedRowsCount(): int
    {
        $count = $this->getFetchedRowsCountImpl();

        $this->logger->logSys(get_class($this) . "::" . __FUNCTION__, $count);

        return $count;
    }

    /**
     * Return the number of fetched rows of the last "SELECT" statement.<br />
     * (to be implemented by child classes).
     *
     * @return int  The number of fetched rows
     */
    abstract protected function getFetchedRowsCountImpl(): int;

    /**
     * Return the number of affected rows after the last "INSERT", "UPDATE" or "DELETE" statement.
     *
     * @return int  The number of affected rows
     */
    final public function getAffectedRowsCount(): int
    {
        $count = $this->getAffectedRowsCountImpl();

        $this->logger->logSys(get_class($this) . "::" . __FUNCTION__, $count);

        return $count;
    }

    /**
     * Return the number of affected rows after the last "INSERT", "UPDATE" or "DELETE" statement.<br />
     * (to be implemented by child classes).
     *
     * @return int  The number of affected rows
     */
    abstract protected function getAffectedRowsCountImpl(): int;

    /**
     * Return the last insert ID after an "INSERT" statement (works only for tables with autoincrement key).
     *
     * @return int  The last insert ID
     */
    final public function getLastInsertId(): int
    {
        $id = $this->getLastInsertIdImpl();

        $this->logger->logSys(get_class($this) . "::" . __FUNCTION__, $id);

        return $id;
    }

    /**
     * Return the last insert ID after an "INSERT" statement (works only for tables with autoincrement key!).<br />
     * (to be implemented by child classes).
     *
     * @return int  The last insert ID
     */
    abstract protected function getLastInsertIdImpl(): int;

    /**
     * Return all column names of the given table.
     *
     * @param string $tableName  Name of the table to determine the column names of
     *
     * @return string[]  The names of the table's columns
     */
    final public function getColumnNames(string $tableName): array
    {
        if (!isset($this->columnNames[$tableName])) {
            $this->columnNames[$tableName] = $this->getColumnNamesImpl($tableName);
        }
        return $this->columnNames[$tableName];
    }

    /**
     * Return all column names of the given table.<br />
     * (to be implemented by child classes).
     *
     * @param string $tableName  Name of the table to determine the column names of
     *
     * @return string[]  The names of the table's columns
     */
    abstract protected function getColumnNamesImpl(string $tableName): array;

    /**
     * Start a transaction.
     *
     * @return bool  Was the operation successful?
     */
    final public function startTransaction(): bool
    {
        $res = $this->startTransactionImpl();

        if ($res) {
            $this->logger->logSys(get_class($this) . "::" . __FUNCTION__, "Transaction successfully started");
        } else {
            $this->errLogger->logSys(
                get_class($this) . "::" . __FUNCTION__,
                "Transaction could not be started",
                Logger::WARN
            );
        }
        return $res;
    }

    /**
     * Start a transaction.<br />
     * (to be implemented by child classes).
     *
     * @return bool  Was the operation succesful?
     */
    abstract protected function startTransactionImpl(): bool;

    /**
     * Commit the current transaction.
     *
     * @return bool  Was the operation successful?
     */
    final public function commitTransaction(): bool
    {
        $res = $this->commitTransactionImpl();

        if ($res) {
            $this->logger->logSys(get_class($this) . "::" . __FUNCTION__, "Transaction successfully commited");
        } else {
            $this->errLogger->logSys(
                get_class($this) . "::" . __FUNCTION__,
                "Transaction could not be commited",
                Logger::WARN
            );
        }
        return $res;
    }

    /**
     * Commit the current transaction. <br />
     * (to be implemented by child classes).
     *
     * @return bool  Was the operation successful?
     */
    abstract protected function commitTransactionImpl(): bool;

    /**
     * Roll back the current transaction.
     *
     * @return bool  Was the operation successful?
     */
    final public function rollbackTransaction(): bool
    {
        $res = $this->rollbackTransactionImpl();

        if ($res) {
            $this->logger->logSys(get_class($this) . "::" . __FUNCTION__, "Transaction successfully rollbacked");
        } else {
            $this->errLogger->logSys(
                get_class($this) . "::" . __FUNCTION__,
                "Transaction could not be rollbacked",
                Logger::WARN
            );
        }
        return $res;
    }

    /**
     * Roll back the current transaction. <br />
     * (to be implemented by child classes).
     *
     * @return bool  Was the operation successful?
     */
    abstract protected function rollbackTransactionImpl(): bool;

    /**
     * Create a Table object for the table with the given name.
     *
     * @param string $tableName  Name of the database table
     * @param string $orderBy    The column to order by
     * @param string $orderDir   The direction to order by ('ASC' or 'DESC')
     *
     * @return Table  The created table object
     */
    public function createTableObj(string $tableName, string $orderBy = null, string $orderDir = 'ASC'): Table
    {
        $dbTable = new Table($tableName, $this->config, $orderBy, $orderDir, $this->instanceId);

        return $dbTable;
    }

    /**
     * Quote the given identifier (e.g. table or column name).
     *
     * @param string $string  The string to be quoted
     *
     * @return string  The quoted string
     */
    abstract public function quoteIdentifier(string $string): string;

    /**
     * Escape a string to be safely used in database queries.
     *
     * @param string $string  The string to be escaped
     *
     * @return string  The escaped string
     */
    abstract public function escapeString(string $string): string;

    /**
     * Unescape a string.
     *
     * @param string $string  The string to be unescaped
     *
     * @return string  The unescaped string
     */
    abstract public function unEscapeString(string $string): string;
}

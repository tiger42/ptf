<?php

namespace Ptf\Model\DB;

use Ptf\App\Config\DB as DBConfig;
use Ptf\App\Context;
use Ptf\Model\DB;
use Ptf\Util\Logger;

/**
 * Class representing a single database table.
 */
class Table implements \ArrayAccess
{
    use \Ptf\Traits\ArrayAccess;

    /** Join type for inner join */
    public const INNER_JOIN       = 'INNER JOIN';
    /** Join type for left join */
    public const LEFT_OUTER_JOIN  = 'LEFT OUTER JOIN';
    /** Join type for left join */
    public const LEFT_JOIN        = 'LEFT OUTER JOIN';
    /** Join type for right join */
    public const RIGHT_OUTER_JOIN = 'RIGHT OUTER JOIN';
    /** Join type for right join */
    public const RIGHT_JOIN       = 'RIGHT OUTER JOIN';

    /*
     * Compare modes for all fetch...() and delete() methods, may be combined with each other
     */
    /** Compare with "=" (default) */
    public const COMP_EQ   =   1;
    /** Compare with ">" */
    public const COMP_GT   =   2;
    /** Compare with ">=" */
    public const COMP_GTE  =   3;
    /** Compare with "<" */
    public const COMP_LT   =   4;
    /** Compare with "<=" */
    public const COMP_LTE  =   5;
    /** Compare with "<>" */
    public const COMP_NE   =   6;
    /** Compare with "LIKE" instead of "=", cannot be combined with any lower compare mode! */
    public const COMP_LIKE =  64;
    /** Compare case-insensitive */
    public const COMP_CI   = 128;

    /** @var Context  The application's context */
    protected $context;

    /** @var DB  Database object */
    protected $db;

    /** @var Logger  The system logger */
    protected $logger;

    /** @var Logger  The error logger */
    protected $errLogger;

    /** @var string  Name of the database table */
    protected $tableName;

    /** @var string  Quoted name of the database table */
    protected $quotedName;

    /** @var string  Name of the database */
    protected $dbName;

    /** @var array  "ORDER BY" columns and directions */
    protected $order;

    /** @var array  Array of member variables (for magic functions) */
    protected $fields;

    /** @var array  Aliases for table columns */
    protected $aliases;

    /** @var string[]  Names of all table columns */
    protected $columns;

    /** @var array  Compare modes of the columns */
    protected $compModes;

    /** @var array  Joined table objects */
    protected $joinTables;

    /** @var bool  Has the fetch() function already been executed? */
    protected $fetched;

    /** @var bool  Do no use the asterisk (*) operator in the next query */
    protected $suppressAsterisk;

    /**
     * Initialize the member variables.
     *
     * @param string   $tableName  Name of the database table
     * @param DBConfig $config     The DB configuration
     * @param Context  $context    The application's context
     * @param string   $id         Optional ID to get different DB instances for same config
     */
    public function __construct(string $tableName, DBConfig $config, Context $context, string $id = '')
    {
        $this->context    = $context;
        $this->db         = DB::getInstance($config, $context, $id);
        $this->dbName     = $config->getDatabase();
        $this->tableName  = $tableName;
        $this->quotedName = $this->db->quoteIdentifier($tableName);

        $this->initFetchVars();

        $this->logger    = $context->getLogger('system');
        $this->errLogger = $context->getLogger('error');
    }

    /**
     * Free the database object.
     */
    public function __destruct()
    {
        unset($this->db);
    }

    /**
     * Get the given field's value.<br />
     * (magic getter function).
     *
     * @param string $name  Name of the field to get the value of
     *
     * @return mixed  The value of the field
     */
    public function __get(string $name)
    {
        $name = strtolower($name);

        return $this->fields[$name] ?? null;
    }

    /**
     * Set the given field's value.<br />
     * (magic setter function).
     *
     * @param string $name   Name of the field to get the value of
     * @param mixed  $value  The value to set, use an array to generate an "IN" statement
     */
    public function __set(string $name, $value): void
    {
        $this->fields[strtolower($name)] = $value;
    }

    /**
     * Determine whether the given field is set.<br />
     * (magic isset function).
     *
     * @param string $name  Name of the field to check
     *
     * @return bool  Is the field set?
     */
    public function __isset(string $name): bool
    {
        return isset($this->fields[strtolower($name)]);
    }

    /**
     * Unset the given field.
     *
     * @param string $name  The name of the field to unset
     */
    public function __unset(string $name): void
    {
        unset($this->fields[strtolower($name)]);
    }

    /**
     * Return the table's name.
     *
     * @return string  The name of the table
     */
    public function getName(): string
    {
        return $this->tableName;
    }

    /**
     * Return the name of the database.
     *
     * @return string  The name of the database
     */
    public function getDBName(): string
    {
        return $this->dbName;
    }

    /**
     * Return the names of all table columns.
     *
     * @return string[]  The names of the table's columns
     */
    public function getColumnNames(): array
    {
        if ($this->columns === null) {
            $this->columns = $this->db->getColumnNames($this->tableName);
        }

        return $this->columns;
    }

    /**
     * Return all set column aliases.
     *
     * @return array  The set aliases
     */
    public function getAliases(): array
    {
        return $this->aliases;
    }

    /**
     * Set/add a column alias.
     *
     * @param string $col    The column to set the alias for
     * @param string $alias  The name of the alias to set
     *
     * @return Table  The table object (for fluent interface)
     */
    public function setAlias(string $col, string $alias): self
    {
        $this->aliases[strtolower($col)] = $alias;

        return $this;
    }

    /**
     * Set the compare mode of the given column (for all fetch...() and delete() functions).
     *
     * @param  string $col   The column to set the compare mode of
     * @param  int    $mode  The mode to set
     *
     * @return Table  The table object (for fluent interface)
     */
    public function setCompareMode(string $col, int $mode): self
    {
        $this->compModes[strtolower($col)] = $mode;

        return $this;
    }

    /**
     * Get the currently set compare mode for the given column.
     *
     * @param string $col  The column to get the compare mode of
     *
     * @return int  The compare mode
     */
    public function getCompareMode(string $col): int
    {
        $col = strtolower($col);

        return $this->compModes[$col] ?? self::COMP_EQ;
    }

    /**
     * Return all set fields as an associative array.
     *
     * @return array  All set fields with their current values
     */
    public function getFields(): array
    {
        return $this->fields;
    }

    /**
     * Copy all fields from the given source array into the internal fields array.
     *
     * @param array    $source  The source array to copy from
     * @param callable $filter  A filter function to be applied to every array value
     *
     * @return Table  The table object (for fluent interface)
     */
    public function fromArray(array $source, callable $filter = null): self
    {
        foreach ($source as $key => $value) {
            if ($filter !== null) {
                $this->$key = call_user_func($filter, $value);
            } else {
                $this->$key = $value;
            }
        }

        return $this;
    }

    /**
     * Initialize the member variables for fetch() function.
     *
     * @param bool $unjoinTables  Also remove all table joins?
     */
    protected function initFetchVars(bool $unjoinTables = true): void
    {
        $this->fields     = [];
        $this->aliases    = [];
        $this->fetched    = false;
        $this->compModes  = [];
        $this->order      = [];
        $this->suppressAsterisk = false;

        if ($unjoinTables) {
            $this->joinTables = [];
        }
    }

    /**
     * Clear all values, reset the search.
     *
     * @param bool $unjoinTables  Also remove all table joins?
     *
     * @return Table  The table object (for fluent interface)
     */
    public function clear(bool $unjoinTables = true): self
    {
        $this->initFetchVars($unjoinTables);

        return $this;
    }

    /**
     * Set/add an "ORDER BY" column.
     *
     * @param string $orderBy   Name of column to order by
     * @param string $orderDir  The order direction ("ASC" or "DESC")
     *
     * @return Table  The table object (for fluent interface)
     */
    public function setOrder(string $orderBy, string $orderDir = 'ASC'): self
    {
        if (!is_numeric($orderBy)) {
            if (strpos($orderBy, '.') !== false) {
                $arr = explode('.', $orderBy, 2);
                $orderBy = $this->db->quoteIdentifier($arr[0])
                    . '.' . $this->db->quoteIdentifier(strtolower($arr[1]));
            } else {
                $orderBy = $this->db->quoteIdentifier(strtolower($orderBy));
            }
        }

        $orderDir = strtoupper($orderDir);
        if ($orderDir != 'ASC' && $orderDir != 'DESC') {
            $orderDir = 'ASC';
        }

        $this->order[$orderBy] = $orderDir;

        return $this;
    }

    /**
     * Join the current table with the given Table object.
     *
     * @param Table  $table   The table to join with
     * @param string $onCond  The "ON" part of the join
     * @param string $type    The join type
     *
     * @throws \InvalidArgumentException  If an invalid join type was given
     *
     * @return Table  The table object (for fluent interface)
     */
    public function join(Table $table, string $onCond, string $type = self::INNER_JOIN): self
    {
        $reflection = new \ReflectionClass(__CLASS__);
        if (!in_array($type, $reflection->getConstants())) {
            throw new \InvalidArgumentException(get_class($this) . '::' . __FUNCTION__ . ': Invalid join type: ' . $type);
        }

        $joinTbl = [
            'table'     => $table,
            'condition' => $onCond,
            'type'      => $type
        ];

        $key = $table->getDBName() . '.' . $table->getName();
        $this->joinTables[$key] = $joinTbl;

        return $this;
    }

    /**
     * Generate the "WHERE" part for the fetch() and delete() functions.
     *
     * @return string  The generated "WHERE" string
     */
    protected function generateWhereCondition(): string
    {
        $str = '';
        if (count($this->fields)) {
            $cond = [];
            foreach ($this->fields as $col => $value) {
                $where = '';
                $identifier = $this->quotedName . '.' . $this->db->quoteIdentifier($col);

                if ($value === null) {
                    $where .= $identifier . ' IS NULL';
                } elseif (is_array($value)) {
                    if ($this->getCompareMode($col) & self::COMP_CI) {
                        $identifier = 'UPPER(' . $identifier . ')';
                        $value = array_map('strtoupper', $value);
                    }
                    $value = array_map([$this->db, 'escapeString'], $value);
                    $where .= $identifier . ' IN (\'' . implode('\', \'', $value) . '\')';
                } else {
                    $value = '\'' . $this->db->escapeString($value) . '\'';
                    $compMode = $this->getCompareMode($col);

                    $comp = ' ';
                    if ($compMode & (self::COMP_LT | self::COMP_GT | self::COMP_EQ | self::COMP_LIKE)) {
                        if ($compMode & self::COMP_LT) {
                            $comp .= '<';
                        }
                        if ($compMode & self::COMP_GT) {
                            $comp .= '>';
                        }
                        if ($compMode & self::COMP_EQ) {
                            $comp .= '=';
                        }
                        if ($compMode & self::COMP_LIKE) {   // "LIKE" overrides all other modes
                            $comp = ' LIKE';
                        }
                    } else {
                        $comp .= '=';   // Default, if nothing was set
                    }
                    $comp .= ' ';

                    if ($compMode & self::COMP_CI) {
                        $identifier = 'UPPER(' . $identifier . ')';
                        $value      = strtoupper($value);
                    }
                    $where .= $identifier . $comp . $value;
                }
                $cond[] = $where;
            }
            $str .= implode(' AND ', $cond);
        }

        foreach ($this->joinTables as $joinTbl) {
            $table = $joinTbl['table'];
            $cond  = $table->getWhereCondition();
            $str .= strlen($cond) ? ' AND ' . $cond : '';
        }

        return $str;
    }

    /**
     * Return the generated "WHERE" part for queries.
     *
     * @return string  The generated "WHERE" string
     */
    public function getWhereCondition(): string
    {
        return $this->generateWhereCondition();
    }

    /**
     * Fetch a row from the table.
     *
     * @param int    $offset      Offset of first row to fetch
     * @param int    $rowCount    Number of rows to fetch, NULL for all
     * @param string $where       "WHERE" part of statement (overrides set fields!)
     * @param array  $additional  Additional special fields to fetch (e.g. "COUNT(id) AS count_id")
     *
     * @return bool  Did the fetch return a result?
     */
    public function fetch(int $offset = 0, int $rowCount = null, string $where = '', array $additional = []): bool
    {
        if (!$this->fetched) {
            $query = 'SELECT ';
            $join  = '';
            $needWhere = false;
            if (!count($this->aliases) && !count($this->joinTables)) {
                if (!$this->suppressAsterisk) {
                    $query .= '*';
                }
                if (count($additional)) {
                    $query .= ($this->suppressAsterisk ? '' : ', ') . implode(', ', $additional);
                }
                $this->suppressAsterisk = false;
            } else {
                $arr = [];
                foreach ($this->getColumnNames() as $col) {
                    $col = strtolower($col);
                    $sel = $this->quotedName . '.' . $this->db->quoteIdentifier($col);
                    if (isset($this->aliases[$col])) {
                        $sel .= ' AS ' . $this->db->quoteIdentifier($this->aliases[$col]);
                    }
                    $arr[] = $sel;
                }
                $query .= implode(', ', $arr);
                if (count($additional)) {
                    $query .= ', ' . implode(', ', $additional);
                }

                foreach ($this->joinTables as $joinTbl) {
                    $arr = [];
                    foreach ($joinTbl['table']->getColumnNames() as $col) {
                        $col = strtolower($col);
                        $sel = $this->db->quoteIdentifier($joinTbl['table']->getName())
                            . '.' . $this->db->quoteIdentifier($col);
                        $aliases = $joinTbl['table']->getAliases();
                        if (isset($aliases[$col])) {
                            $sel .= ' AS ' . $this->db->quoteIdentifier($aliases[$col]);
                        }
                        $arr[] = $sel;
                    }
                    $query .= ', ' . implode(', ', $arr);

                    $join .= ' ' . $joinTbl['type'] . ' '
                        . $this->db->quoteIdentifier($joinTbl['table']->getDBName())
                        . '.' . $this->db->quoteIdentifier($joinTbl['table']->getName())
                        . ' ON ' . $joinTbl['condition'];

                    if (count($joinTbl['table']->getFields())) {
                        $needWhere = true;
                    }
                }
            }
            $query .= ' FROM ' . $this->quotedName . $join;

            if (strlen($where)) {
                $query .= ' WHERE ' . $where;
            } elseif (count($this->fields) || $needWhere) {
                $query .= ' WHERE ' . $this->generateWhereCondition();
            }
            if (count($this->order)) {
                $query .= ' ORDER BY ';
                $order = [];
                foreach ($this->order as $orderBy => $orderDir) {
                    $order[] = $orderBy . ' ' . $orderDir;
                }
                $query .= implode(', ', $order);
            }

            $msg = $query . ($offset > 0 || $rowCount !== null ? '; Offset: ' . $offset . ', Count: ' . $rowCount : '');
            $this->logger->logSys(get_class($this) . '::' . __FUNCTION__, $msg);

            $this->db->query($query, $offset, $rowCount);

            $this->fetched = true;
        }

        if (!($row = $this->db->fetch())) {
            return false;
        }
        $this->fields = array_map([$this->db, 'unEscapeString'], $row);

        return true;
    }

    /**
     * Perform a fetch and return the result as an array.
     *
     * @param int    $offset    Offset of first row to fetch
     * @param int    $rowCount  Number of rows to fetch
     * @param string $where     "WHERE" part of statement (overrides set fields!)
     *
     * @return array|false  The result row as an array; FALSE, if no result
     */
    public function fetchArray(int $offset = 0, int $rowCount = null, string $where = '')
    {
        if (!$this->fetch($offset, $rowCount, $where)) {
            return false;
        }

        return $this->fields;
    }

    /**
     * Fetch a "special" value ("COUNT", "MAX", "MIN" etc.) of the given table column.
     *
     * @param string $function  The SQL function to execute on the given column
     * @param string $col       The column to apply the function to
     * @param string $where     "WHERE" part of statement (overrides set fields!)
     *
     * @return array|false  The query result; FALSE if no result
     */
    protected function fetchSpecial(string $function, string $col, string $where = '')
    {
        $special = $function . '(' . $col . ')';
        $this->suppressAsterisk = true;

        return $this->fetch(0, null, $where, [$special]) ? $this->{$special} : false;
    }

    /**
     * Perform a "COUNT" query on the given column.
     *
     * @param string $col    The column to count the value of
     * @param string $where  "WHERE" part of statement (overrides set fields!)
     *
     * @return int|false  The number of rows; FALSE, if no result
     */
    public function count(string $col = '*', string $where = '')
    {
        $count = $this->fetchSpecial('COUNT', $col, $where);
        $this->logger->logSys(get_class($this) . '::' . __FUNCTION__, 'COUNT of "' . $col . '": ' . $count);

        return $count === false ? false : (int)$count;
    }

    /**
     * Fetch maximum value of a given table column.
     *
     * @param string $col    Column to find maximum value of
     * @param string $where  "WHERE" part of statement (overrides set fields!)
     *
     * @return int|false  The maximum value of the column; FALSE, if no result
     */
    public function fetchMaxValue(string $col, string $where = '')
    {
        $max = $this->fetchSpecial('MAX', $col, $where);
        $this->logger->logSys(get_class($this) . '::' . __FUNCTION__, 'MAX of "' . $col . '": ' . $max);

        return $max;
    }

    /**
     * Fetch minimum value of a given table column.
     *
     * @param string $col    Column to find minimum value of
     * @param string $where  "WHERE" part of statement (overrides set fields!)
     *
     * @return int|false  The minimum value of the column; FALSE, if no result
     */
    public function fetchMinValue($col, $where = '')
    {
        $min = $this->fetchSpecial('MIN', $col, $where);
        $this->logger->logSys(get_class($this) . '::' . __FUNCTION__, 'MIN of "' . $col . '": ' . $min);

        return $min;
    }

    /**
     * Insert a row into the database table.
     *
     * @param bool $replace  Use "REPLACE" instead of "INSERT"? (MySQL specific)
     *
     * @return Table  The table object (for fluent interface)
     */
    public function insert(bool $replace = false): self
    {
        $arr = [];
        foreach ($this->fields as $col => $value) {
            $val = $value === null ? 'NULL' : ('\'' . $this->db->escapeString($value) . '\'');
            $arr[$col] = $val;
        }
        $cols = implode(', ', array_keys($arr));
        $vals = implode(', ', $arr);

        $cmd = $replace ? 'REPLACE' : 'INSERT';
        $sql =
            $cmd . ' INTO ' . $this->quotedName
            . ' (' . $cols . ') VALUES (' . $vals . ')';

        $this->logger->logSys(get_class($this) . '::' . __FUNCTION__, $sql);

        $this->db->execSql($sql);

        return $this;
    }

    /**
     * Update rows of the database table.
     *
     * @param string $where  "WHERE" part of the update statement
     *
     * @return Table  The table object (for fluent interface)
     */
    public function update(string $where): self
    {
        $arr = [];
        foreach ($this->fields as $col => $value) {
            $set =
                $this->db->quoteIdentifier($col) . ' = '
                . ($value === null ? 'NULL' : ('\'' . $this->db->escapeString($value) . '\''));

            $arr[] = $set;
        }
        $sets = implode(', ', $arr);

        $sql =
            'UPDATE ' . $this->quotedName
            . ' SET ' . $sets
            . ' WHERE ' . $where;

        $this->logger->logSys(get_class($this) . '::' . __FUNCTION__, $sql);

        $this->db->execSql($sql);

        return $this;
    }

    /**
     * Delete rows from the database table.
     *
     * @param string $where  "WHERE" part of the delete statement (overrides set fields!)
     *
     * @throws \InvalidArgumentException  If no valid "WHERE" condition was given
     *
     * @return Table  The table object (for fluent interface)
     */
    public function delete(string $where = ''): self
    {
        if (strlen($where) && (!is_string($where) || is_numeric($where))) {
            $message = 'WHERE parameter must be string or empty';
            $this->errLogger->logSys(get_class($this) . '::' . __FUNCTION__, $message, \Ptf\Util\Logger::FATAL);

            throw new \InvalidArgumentException(get_class($this) . '::' . __FUNCTION__ . ': ' . $message);
        }

        $sql =
            'DELETE FROM ' . $this->quotedName
            . ' WHERE ';

        if (strlen($where)) {
            $sql .= $where;
        } elseif (count($this->fields)) {
            $sql .= $this->generateWhereCondition();
        } else {
            $message = 'No WHERE condition set';
            $this->errLogger->logSys(get_class($this) . '::' . __FUNCTION__, $message, \Ptf\Util\Logger::FATAL);

            throw new \InvalidArgumentException(get_class($this) . '::' . __FUNCTION__ . ': ' . $message);
        }

        $this->logger->logSys(get_class($this) . '::' . __FUNCTION__, $sql);

        $this->db->execSql($sql);

        return $this;
    }

    /**
     * Return the number of fetched rows of the last fetch().
     *
     * @return int  The number of fetched rows
     */
    public function getFetchedRowsCount(): int
    {
        return $this->db->getFetchedRowsCount();
    }

    /**
     * Return the number of affected rows after the last insert(), update() or delete().
     *
     * @return int  The number of affected rows
     */
    public function getAffectedRowsCount(): int
    {
        return $this->db->getAffectedRowsCount();
    }

    /**
     * Return the last insert ID after insert().<br />
     * (only if table has an autoincrement key).
     *
     * @return int  The last insert ID
     */
    public function getLastInsertId(): int
    {
        return $this->db->getLastInsertId();
    }

    /**
     * Return the internal DB object.
     *
     * @return DB  The DB object
     */
    public function getDB(): DB
    {
        return $this->db;
    }
}

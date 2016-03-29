<?php

namespace Ptf\Model\DB;

/**
 * Class representing a single database table
 */
class Table implements \ArrayAccess
{
    use \Ptf\Traits\ArrayAccess;

    /** Join type for inner join */
    const INNER_JOIN       = 'INNER JOIN';
    /** Join type for left join */
    const LEFT_OUTER_JOIN  = 'LEFT OUTER JOIN';
    /** Join type for left join */
    const LEFT_JOIN        = 'LEFT OUTER JOIN';
    /** Join type for right join */
    const RIGHT_OUTER_JOIN = 'RIGHT OUTER JOIN';
    /** Join type for right join */
    const RIGHT_JOIN       = 'RIGHT OUTER JOIN';

    /*
     * Compare modes for all fetch...() and delete() methods, may be combined with each other
     */
    /** Compare with "=" (default) */
    const COMP_EQ   =   1;
    /** Compare with ">" */
    const COMP_GT   =   2;
    /** Compare with ">=" */
    const COMP_GTE  =   3;
    /** Compare with "<" */
    const COMP_LT   =   4;
    /** Compare with "<=" */
    const COMP_LTE  =   5;
    /** Compare with "<>" */
    const COMP_NE   =   6;
    /** Compare with "LIKE" instead of "=", cannot be combined with any lower compare mode! */
    const COMP_LIKE =  64;
    /** Compare case-insensitive */
    const COMP_CI   = 128;

    /**
     * The application's context
     * @var \Ptf\App\Context
     */
    protected $context;

    /**
     * Database object
     * @var \Ptf\Model\DB
     */
    protected $db;

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
     * Name of the database table
     * @var string
     */
    protected $tableName;

    /**
     * Quoted name of the database table
     * @var string
     */
    protected $quotedName;

    /**
     * Name of the database
     * @var string
     */
    protected $dbName;

    /**
     * "ORDER BY" columns and directions
     * @var array
     */
    protected $order;

    /**
     * Array of member variables (for magic functions)
     * @var array
     */
    protected $fields;

    /**
     * Aliases for table columns
     * @var array
     */
    protected $aliases;

    /**
     * Names of all table columns
     * @var string[]
     */
    protected $columns;

    /**
     * Compare modes of the columns
     * @var array
     */
    protected $compModes;

    /**
     * Joined table objects
     * @var array
     */
    protected $joinTables;

    /**
     * Has the fetch() function already been executed?
     * @var boolean
     */
    protected $fetched;

    /**
     * Do no use the asterisk (*) operator in the next query
     * @var boolean
     */
    protected $suppressAsterisk;

    /**
     * Initialize the member variables
     *
     * @param   string $tableName           Name of the database table
     * @param   \Ptf\App\Config\DB $config  The DB configuration
     * @param   \Ptf\App\Context $context   The application's context
     * @param   string $id                  Optional ID to get different DB instances for same config
     */
    public function __construct($tableName, \Ptf\App\Config\DB $config, \Ptf\App\Context $context, $id = '')
    {
        $this->context    = $context;
        $this->db         = \Ptf\Model\DB::getInstance($config, $context, $id);
        $this->dbName     = $config->getDatabase();
        $this->tableName  = $tableName;
        $this->quotedName = $this->db->quoteIdentifier($tableName);

        $this->initFetchVars();

        $this->logger    = $context->getLogger('system');
        $this->errLogger = $context->getLogger('error');
    }

    /**
     * Free the database object
     */
    public function __destruct()
    {
        unset($this->db);
    }

    /**
     * Get the given field's value.<br />
     * (magic getter function)
     *
     * @param   string $name                Name of the field to get the value of
     * @return  mixed                       The value of the field
     */
    public function __get($name)
    {
        $name = strtolower($name);
        return isset($this->fields[$name]) ? $this->fields[$name] : null;
    }

    /**
     * Set the given field's value.<br />
     * (magic setter function)
     *
     * @param   string $name                Name of the field to get the value of
     * @param   mixed $value                The value to set, use an array to generate an "IN" statement
     */
    public function __set($name, $value)
    {
        $this->fields[strtolower($name)] = $value;
    }

    /**
     * Determine whether the given field is set.<br />
     * (magic isset function)
     *
     * @param   string $name                Name of the field to check
     * @return  boolean                     Is the field set?
     */
    public function __isset($name)
    {
        return isset($this->fields[strtolower($name)]);
    }

    /**
     * Unset the given field
     *
     * @param   string $name                The name of the field to unset
     */
    public function __unset($name)
    {
        unset($this->fields[strtolower($name)]);
    }

    /**
     * Return the table's name
     *
     * @return  string                      The name of the table
     */
    public function getName()
    {
        return $this->tableName;
    }

    /**
     * Return the name of the database
     *
     * @return  string                      The name of the database
     */
    public function getDBName()
    {
        return $this->dbName;
    }

    /**
     * Return the names of all table columns
     *
     * @return  string[]                    The names of the table's columns
     */
    public function getColumnNames()
    {
        if ($this->columns === null) {
            $this->columns = $this->db->getColumnNames($this->tableName);
        }
        return $this->columns;
    }

    /**
     * Return all set column aliases
     *
     * @return  array                       The set aliases
     */
    public function getAliases()
    {
        return $this->aliases;
    }

    /**
     * Set/add a column alias
     *
     * @param   string $col                 The column to set the alias for
     * @param   string $alias               The name of the alias to set
     * @return  \Ptf\Model\DB\Table         The table object (for fluent interface)
     */
    public function setAlias($col, $alias)
    {
        $this->aliases[strtolower($col)] = $alias;

        return $this;
    }

    /**
     * Set the compare mode of the given column (for all fetch...() and delete() functions)
     *
     * @param   string $col                 The column to set the compare mode of
     * @param   integer $mode               The mode to set
     * @return  \Ptf\Model\DB\Table         The table object (for fluent interface)
     */
    public function setCompareMode($col, $mode)
    {
        $this->compModes[strtolower($col)] = $mode;

        return $this;
    }

    /**
     * Get the currently set compare mode for the given column
     *
     * @param   string $col                 The column to get the compare mode of
     * @return  integer                     The compare mode
     */
    public function getCompareMode($col)
    {
        $col = strtolower($col);
        if (isset($this->compModes[$col])) {
            return $this->compModes[$col];
        }
        return self::COMP_EQ;
    }

    /**
     * Return all set fields as an associative array
     *
     * @return  array                       All set fields with their current values
     */
    public function getFields()
    {
        return $this->fields;
    }

    /**
     * Copy all fields from the given source array into the internal fields array
     *
     * @param   array $source               The source array to copy from
     * @param   callable $filter            A filter function to be applied to every array value
     * @return  \Ptf\Model\DB\Table         The table object (for fluent interface)
     */
    public function fromArray(array $source, callable $filter = null)
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
     * Initialize the member variables for fetch() function
     *
     * @param   boolean $unjoinTables       Also remove all table joins?
     */
    protected function initFetchVars($unjoinTables = true)
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
     * Clear all values, reset the search
     *
     * @param   boolean $unjoinTables       Also remove all table joins?
     * @return  \Ptf\Model\DB\Table         The table object (for fluent interface)
     */
    public function clear($unjoinTables = true)
    {
        $this->initFetchVars($unjoinTables);

        return $this;
    }

    /**
     * Set/add an "ORDER BY" column
     *
     * @param   string $orderBy             Name of column to order by
     * @param   string $orderDir            The order direction ("ASC" or "DESC")
     * @return  \Ptf\Model\DB\Table         The table object (for fluent interface)
     */
    public function setOrder($orderBy, $orderDir = 'ASC')
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
     * Join the current table with the given Table object
     *
     * @param   \Ptf\Model\DB\Table $table  The table to join with
     * @param   string $onCond              The "ON" part of the join
     * @param   string $type                The join type
     * @return  \Ptf\Model\DB\Table         The table object (for fluent interface)
     * @throws  \InvalidArgumentException   If an invalid join type was given
     */
    public function join(\Ptf\Model\DB\Table $table, $onCond, $type = self::INNER_JOIN)
    {
        $reflection = new \ReflectionClass(__CLASS__);
        if (!in_array($type, $reflection->getConstants())) {
            throw new \InvalidArgumentException(get_class($this) . "::" . __FUNCTION__ . ": Invalid join type: " . $type);
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
     * Generate the "WHERE" part for the fetch() and delete() functions
     *
     * @return  string                      The generated "WHERE" string
     */
    protected function generateWhereCondition()
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
                        $comp .= '=';   // Default, if nothing set
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
     * Return the generated "WHERE" part for queries
     *
     * @return  string                      The generated "WHERE" string
     */
    public function getWhereCondition()
    {
        return $this->generateWhereCondition();
    }

    /**
     * Fetch a row from the table
     *
     * @param   integer $offset             Offset of first row to fetch
     * @param   integer $rowCount           Number of rows to fetch, NULL for all
     * @param   string $where               "WHERE" part of statement (overrides set fields!)
     * @param   array $additional           Additional special fields to fetch (e.g. "COUNT(id) AS count_id")
     * @return  boolean                     Did the fetch return a result?
     */
    public function fetch($offset = 0, $rowCount = null, $where = '', array $additional = [])
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

            $msg = $query . ($offset > 0 || $rowCount !== null ? "; Offset: " . $offset . ", Count: " . $rowCount : "");
            $this->logger->logSys(get_class($this) . "::" . __FUNCTION__, $msg);

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
     * Perform a fetch and return the result as an array
     *
     * @param   integer $offset             Offset of first row to fetch
     * @param   integer $rowCount           Number of rows to fetch
     * @param   string $where               "WHERE" part of statement (overrides set fields!)
     * @return  mixed                       The result row as an array; FALSE, if no result
     */
    public function fetchArray($offset = 0, $rowCount = null, $where = '')
    {
        if (!$this->fetch($offset, $rowCount, $where)) {
            return false;
        }

        return $this->fields;
    }

    /**
     * Fetch a "special" value ("COUNT", "MAX", "MIN" etc.) of the given table column
     *
     * @param   string $function            The SQL function to execute on the given column
     * @param   string $col                 The column to apply the function to
     * @param   string $where               "WHERE" part of statement (overrides set fields!)
     * @return  mixed                       The query result, FALSE if none
     */
    protected function fetchSpecial($function, $col, $where = '')
    {
        $special = $function . '(' . $col . ')';
        $this->suppressAsterisk = true;

        return $this->fetch(0, null, $where, [$special]) ? $this->{$special} : false;
    }

    /**
     * Perform a "COUNT" query on the given column
     *
     * @param   string $col                 The column to count the value of
     * @param   string $where               "WHERE" part of statement (overrides set fields!)
     * @return  mixed                       The number of rows; FALSE, if no result
     */
    public function count($col = '*', $where = '')
    {
        $count = $this->fetchSpecial('COUNT', $col, $where);
        $this->logger->logSys(get_class($this) . "::" . __FUNCTION__, "COUNT of '" . $col . "': " . $count);

        return $count === false ? false : (int)$count;
    }

    /**
     * Fetch maximum value of a given table column
     *
     * @param   string $col                 Column to find maximum value of
     * @param   string $where               "WHERE" part of statement (overrides set fields!)
     * @return  mixed                       The maximum value of the column; FALSE, if no result
     */
    public function fetchMaxValue($col, $where = '')
    {
        $max = $this->fetchSpecial('MAX', $col, $where);
        $this->logger->logSys(get_class($this) . "::" . __FUNCTION__, "MAX of '" . $col . "': " . $max);

        return $max;
    }

    /**
     * Fetch minimum value of a given table column
     *
     * @param   string $col                 Column to find minimum value of
     * @param   string $where               "WHERE" part of statement (overrides set fields!)
     * @return  mixed                       The minimum value of the column; FALSE, if no result
     */
    public function fetchMinValue($col , $where = '')
    {
        $min = $this->fetchSpecial('MIN', $col, $where);
        $this->logger->logSys(get_class($this) . "::" . __FUNCTION__, "MIN of '" . $col . "': " . $min);

        return $min;
    }

    /**
     * Insert a row into the database table
     *
     * @param   boolean $replace            Use "REPLACE" instead of "INSERT"? (MySQL specific)
     * @return  \Ptf\Model\DB\Table         The table object (for fluent interface)
     */
    public function insert($replace = false)
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

        $this->logger->logSys(get_class($this) . "::" . __FUNCTION__, $sql);

        $this->db->execSql($sql);

        return $this;
    }

    /**
     * Update rows of the database table
     *
     * @param   string $where               "WHERE" part of the update statement
     * @return  \Ptf\Model\DB\Table         The table object (for fluent interface)
     */
    public function update($where)
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

        $this->logger->logSys(get_class($this) . "::" . __FUNCTION__, $sql);

        $this->db->execSql($sql);

        return $this;
    }

    /**
     * Delete rows from the database table
     *
     * @param   string $where               "WHERE" part of the delete statement (overrides set fields!)
     * @return  \Ptf\Model\DB\Table         The table object (for fluent interface)
     * @throws  \InvalidArgumentException   If no valid "WHERE" condition was given
     */
    public function delete($where = '')
    {
        if (strlen($where) && (!is_string($where) || is_numeric($where))) {
            $message = "WHERE parameter must be string or empty";
            $this->errLogger->logSys(get_class($this) . "::" . __FUNCTION__, $message, \Ptf\Util\Logger::FATAL);
            throw new \InvalidArgumentException(get_class($this) . "::" . __FUNCTION__ . ": " . $message);
        }

        $sql =
            'DELETE FROM ' . $this->quotedName
            . ' WHERE ';

        if (strlen($where)) {
            $sql .= $where;
        } elseif (count($this->fields)) {
            $sql .= $this->generateWhereCondition();
        } else {
            $message = "No WHERE condition set";
            $this->errLogger->logSys(get_class($this) . "::" . __FUNCTION__, $message, \Ptf\Util\Logger::FATAL);
            throw new \InvalidArgumentException(get_class($this) . "::" . __FUNCTION__ . ": " . $message);
        }

        $this->logger->logSys(get_class($this) . "::" . __FUNCTION__, $sql);

        $this->db->execSql($sql);

        return $this;
    }

    /**
     * Return the number of fetched rows of the last fetch()
     *
     * @return  integer                     The number of fetched rows
     */
    public function getFetchedRowsCount()
    {
        return $this->db->getFetchedRowsCount();
    }

    /**
     * Return the number of affected rows after the last insert(), update() or delete()
     *
     * @return  integer                     The number of affected rows
     */
    public function getAffectedRowsCount()
    {
        return $this->db->getAffectedRowsCount();
    }

    /**
     * Return the last insert ID after insert().<br />
     * (only if table has an autoincrement key!)
     *
     * @return  integer                     The last insert ID
     */
    public function getLastInsertId()
    {
        return $this->db->getLastInsertId();
    }

    /**
     * Return the internal DB object
     *
     * @return  \Ptf\Model\DB               The DB object
     */
    public function getDB()
    {
        return $this->db;
    }
}

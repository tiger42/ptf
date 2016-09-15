<?php

namespace Ptf\Model\DB;

/**
 * Database wrapper for MySQL access with MySQLi
 */
class MySQLi extends \Ptf\Model\DB
{
    /**
     * MySQLi object
     * @var \MySQLi
     */
    private $db;

    /**
     * Query result object
     * @var \MySQLi_Result
     */
    private $queryRes;

    /**
     * Number of rows of the last "SELECT" result
     * @var integer
     */
    private $numRows;

    /**
     * The number of affected rows after the last "INSERT", "UPDATE" or "DELETE" statement
     * @var integer
     */
    private $affRows;

    /**
     * Disconnect from the database
     */
    public function __destruct()
    {
        $this->db->close();
    }

    /**
     * Connect to the database
     *
     * @throws  \Ptf\Core\Exception\DBConnect If the DB connection could not be established
     */
    protected function connect()
    {
        $this->db = @new \MySQLi(
            $this->config->getHost(),
            $this->config->getUsername(),
            $this->config->getPassword(),
            $this->config->getDatabase(),
            $this->config->getPort()
        );

        if ($this->db->connect_error) {
            $this->errLogger->logSys(get_class($this) . "::" . __FUNCTION__, $this->db->connect_error, \Ptf\Util\Logger::ERROR);
            throw new \Ptf\Core\Exception\DBConnect(get_class($this) . "::" . __FUNCTION__ . ": " . $this->db->connect_error);
        }
        $this->db->set_charset($this->config->getCharset());
    }

    /**
     * Perform a "SELECT" query on the database
     *
     * @param   string $query                The SQL query string
     * @param   integer $offset              Offset of the first row
     * @param   integer $rowCount            Number of rows to fetch
     * @return  integer                      The number of fetched rows
     * @throws  \Ptf\Core\Exception\DBQuery  If the query has failed
     */
    protected function queryImpl($query, $offset = 0, $rowCount = null)
    {
        $limit = '';
        if ($rowCount !== null) {
            $limit = ' LIMIT ' . (int)$offset . ', ' . (int)$rowCount;
        } elseif ($offset > 0) {
            $limit = ' LIMIT ' . (int)$offset . ', 18446744073709551615';
        }

        $res = $this->db->query($query . $limit);

        if (!is_object($res)) {
            $this->errLogger->logSys(get_class($this) . "::" . __FUNCTION__, $this->db->error, \Ptf\Util\Logger::ERROR);
            throw new \Ptf\Core\Exception\DBQuery(get_class($this) . "::" . __FUNCTION__ . ": " . $this->db->error);
        }
        $this->queryRes = $res;
        $this->numRows  = $res->num_rows;

        return $this->numRows;
    }

    /**
     * Fetch a row from the query result, advance the row pointer
     *
     * @return  mixed                       Result row as assoc array; FALSE, if result has no more rows
     */
    public function fetch()
    {
        $row = $this->queryRes->fetch_assoc();

        if (is_array($row)) {
            $row = array_change_key_case($row, CASE_LOWER);
        } else {
            $this->queryRes->free();
            return false;
        }
        return $row;
    }

    /**
     * Perform a manipulation query on the database (e.g. "UPDATE", "INSERT")
     *
     * @param   string $sql                  The SQL statement to execute
     * @throws  \Ptf\Core\Exception\DBQuery  If the query has failed
     */
    protected function execSqlImpl($sql)
    {
        if (!$this->db->query($sql)) {
            $this->errLogger->logSys(get_class($this) . "::" . __FUNCTION__, $this->db->error, \Ptf\Util\Logger::ERROR);
            throw new \Ptf\Core\Exception\DBQuery(get_class($this) . "::" . __FUNCTION__ . ": " . $this->db->error);
        }
        $this->affRows = $this->db->affected_rows;
    }

    /**
     * Return the number of fetched rows of the last "SELECT" statement
     *
     * @return  integer                     The number of fetched rows
     */
    protected function getFetchedRowsCountImpl()
    {
        return $this->numRows;
    }

    /**
     * Return the number of affected rows after the last "INSERT", "UPDATE" or "DELETE" statement
     *
     * @return  integer                     The number of affected rows
     */
    protected function getAffectedRowsCountImpl()
    {
        return $this->affRows;
    }

    /**
     * Return the last insert ID after an "INSERT" statement (works only for tables with autoincrement key!)
     *
     * @return  integer                     The last insert ID
     */
    protected function getLastInsertIdImpl()
    {
        return $this->db->insert_id;
    }

    /**
     * Return all column names of the given table
     *
     * @param   string $tableName           Name of the table to determine the column names of
     * @return  string[]                    The names of the table's columns
     */
    protected function getColumnNamesImpl($tableName)
    {
        $this->query('DESCRIBE ' . $this->quoteIdentifier($tableName));
        $columns = [];
        while (($row = $this->fetch())) {
            $columns[] = $row['field'];
        }
        return $columns;
    }

    /**
     * Start a transaction
     *
     * @return  boolean                     Was the operation successful?
     */
    protected function startTransactionImpl()
    {
        return $this->db->begin_transaction();
    }

    /**
     * Commit the current transaction
     *
     * @return  boolean                     Was the operation successful?
     */
    protected function commitTransactionImpl()
    {
        return $this->db->commit();
    }

    /**
     * Roll back the current transaction
     *
     * @return  boolean                     Was the operation successful?
     */
    protected function rollbackTransactionImpl()
    {
        return $this->db->rollback();
    }

    /**
     * Quote the given identifier (e.g. table or column name)
     *
     * @param   string $string              The string to be quoted
     * @return  string                      The quoted string
     */
    public function quoteIdentifier($string)
    {
        return '`' . $string . '`';
    }

    /**
     * Escape a string to be safely used in database queries
     *
     * @param   string $string              The string to be escaped
     * @return  string                      The escaped string
     */
    public function escapeString($string)
    {
        return $this->db->real_escape_string($string);
    }

    /**
     * Unescape a string
     *
     * @param   string $string              The string to be unescaped
     * @return  string                      The unescaped string
     */
    public function unEscapeString($string)
    {
        return stripslashes($string);
    }
}

<?php

namespace Ptf\Model\DB;

use Ptf\Core\Exception\DBConnect as DBConnectException;
use Ptf\Core\Exception\DBQuery as DBQueryException;

/**
 * Database wrapper for MySQL access via MySQLi.
 */
class MySQLi extends \Ptf\Model\DB
{
    /** @var \MySQLi  MySQLi object */
    private $db;

    /** @var \MySQLi_Result  Query result object */
    private $queryRes;

    /** @var int  Number of rows of the last "SELECT" result */
    private $numRows;

    /** @var int  The number of affected rows after the last "INSERT", "UPDATE" or "DELETE" statement */
    private $affRows;

    /**
     * Disconnect from the database.
     */
    public function __destruct()
    {
        $this->db->close();
    }

    /**
     * Connect to the database.
     *
     * @throws DBConnectException  If the DB connection could not be established
     */
    protected function connect(): void
    {
        $this->db = @new \MySQLi(
            $this->config->getHost(),
            $this->config->getUsername(),
            $this->config->getPassword(),
            $this->config->getDatabase(),
            $this->config->getPort()
        );

        if ($this->db->connect_error) {
            $this->errLogger->logSys(get_class($this) . '::' . __FUNCTION__, $this->db->connect_error, \Ptf\Util\Logger::ERROR);

            throw new DBConnectException(get_class($this) . '::' . __FUNCTION__ . ': ' . $this->db->connect_error);
        }

        $this->db->set_charset($this->config->getCharset());
    }

    /**
     * Perform a "SELECT" query on the database.
     *
     * @param string $query     The SQL query string
     * @param int    $offset    Offset of the first row
     * @param int    $rowCount  Number of rows to fetch
     *
     * @throws DBQueryException  If the query has failed
     *
     * @return int  The number of fetched rows
     */
    protected function queryImpl(string $query, int $offset = 0, int $rowCount = null): int
    {
        $limit = '';
        if ($rowCount !== null) {
            $limit = ' LIMIT ' . (int)$offset . ', ' . (int)$rowCount;
        } elseif ($offset > 0) {
            $limit = ' LIMIT ' . (int)$offset . ', 18446744073709551615';
        }

        $res = $this->db->query($query . $limit);

        if (!is_object($res)) {
            $this->errLogger->logSys(get_class($this) . '::' . __FUNCTION__, $this->db->error, \Ptf\Util\Logger::ERROR);

            throw new DBQueryException(get_class($this) . '::' . __FUNCTION__ . ': ' . $this->db->error);
        }
        $this->queryRes = $res;
        $this->numRows  = $res->num_rows;

        return $this->numRows;
    }

    /**
     * Fetch a row from the query result, advance the row pointer.
     *
     * @return array|false  Result row as assoc array; FALSE, if result has no more rows
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
     * Perform a manipulation query on the database (e.g. "UPDATE", "INSERT").
     *
     * @param string $sql  The SQL statement to execute
     *
     * @throws DBQueryException  If the query has failed
     */
    protected function execSqlImpl(string $sql): void
    {
        if (!$this->db->query($sql)) {
            $this->errLogger->logSys(get_class($this) . '::' . __FUNCTION__, $this->db->error, \Ptf\Util\Logger::ERROR);

            throw new DBQueryException(get_class($this) . '::' . __FUNCTION__ . ': ' . $this->db->error);
        }

        $this->affRows = $this->db->affected_rows;
    }

    /**
     * Return the number of fetched rows of the last "SELECT" statement.
     *
     * @return int The number of fetched rows
     */
    protected function getFetchedRowsCountImpl(): int
    {
        return $this->numRows;
    }

    /**
     * Return the number of affected rows after the last "INSERT", "UPDATE" or "DELETE" statement.
     *
     * @return int  The number of affected rows
     */
    protected function getAffectedRowsCountImpl(): int
    {
        return $this->affRows;
    }

    /**
     * Return the last insert ID after an "INSERT" statement (works only for tables with autoincrement key).
     *
     * @return int  The last insert ID
     */
    protected function getLastInsertIdImpl(): int
    {
        return $this->db->insert_id;
    }

    /**
     * Return all column names of the given table.
     *
     * @param string $tableName  Name of the table to determine the column names of
     *
     * @return string[]  The names of the table's columns
     */
    protected function getColumnNamesImpl(string $tableName): array
    {
        $this->query('DESCRIBE ' . $this->quoteIdentifier($tableName));

        $columns = [];
        while (($row = $this->fetch())) {
            $columns[] = $row['field'];
        }

        return $columns;
    }

    /**
     * Start a transaction.
     *
     * @return bool  Was the operation successful?
     */
    protected function startTransactionImpl(): bool
    {
        return $this->db->begin_transaction();
    }

    /**
     * Commit the current transaction.
     *
     * @return bool  Was the operation successful?
     */
    protected function commitTransactionImpl(): bool
    {
        return $this->db->commit();
    }

    /**
     * Roll back the current transaction.
     *
     * @return bool  Was the operation successful?
     */
    protected function rollbackTransactionImpl(): bool
    {
        return $this->db->rollback();
    }

    /**
     * Quote the given identifier (e.g. table or column name).
     *
     * @param string $string  The string to be quoted
     *
     * @return string  The quoted string
     */
    public function quoteIdentifier(string $string): string
    {
        return '`' . $string . '`';
    }

    /**
     * Escape a string to be safely used in database queries.
     *
     * @param string $string  The string to be escaped
     *
     * @return string  The escaped string
     */
    public function escapeString(string $string): string
    {
        return $this->db->real_escape_string($string);
    }

    /**
     * Unescape a string.
     *
     * @param string $string  The string to be unescaped
     *
     * @return string  The unescaped string
     */
    public function unEscapeString(string $string): string
    {
        return stripslashes($string);
    }
}

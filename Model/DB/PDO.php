<?php

namespace Ptf\Model\DB;

use Ptf\Core\Exception\DBQuery as DBQueryException;

/**
 * Generic database wrapper for PDO
 */
abstract class PDO extends \Ptf\Model\DB
{
    /**
     * PDO database object
     * @var \PDO
     */
    protected $db;

    /**
     * PDO statement object
     * @var \PDOStatement
     */
    protected $statement;

    /**
     * Number of rows of the last "SELECT" result
     * @var int
     */
    protected $numRows;

    /**
     * The number of affected rows after the last "INSERT", "UPDATE" or "DELETE" statement
     * @var int
     */
    protected $affRows;

    /**
     * Disconnect from the database.
     */
    public function __destruct()
    {
        unset($this->db);
    }

    /**
     * Perform a "SELECT" query on the database.
     *
     * @param string $query  The SQL query string
     *
     * @throws DBQueryException  If the query has failed
     *
     * @return int  The number of fetched rows
     */
    protected function runQuery(string $query): int
    {
        $statement = $this->db->query($query);
        if ($statement === false) {
            $error = $this->db->errorInfo();
            $this->errLogger->logSys(get_class($this) . '::' . __FUNCTION__, $error[2], \Ptf\Util\Logger::ERROR);
            throw new DBQueryException(get_class($this) . '::' . __FUNCTION__ . ': ' . $error[2]);
        }
        $this->statement = $statement;
        $this->numRows   = $statement->rowCount();

        return $this->numRows;
    }

    /**
     * Fetch a row from the query result, advance the row pointer.
     *
     * @return array|false  Result row as assoc array; FALSE, if result has no more rows
     */
    public function fetch()
    {
        $row = $this->statement->fetch(\PDO::FETCH_ASSOC);

        if (is_array($row)) {
            $row = array_change_key_case($row, CASE_LOWER);
        } else {
            $this->statement->closeCursor();
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
        $res = $this->db->exec($sql);

        if ($res === false) {
            $error = $this->db->errorInfo();
            $this->errLogger->logSys(get_class($this) . '::' . __FUNCTION__, $error[2], \Ptf\Util\Logger::ERROR);
            throw new \Ptf\Core\Exception\DBQuery(get_class($this) . '::' . __FUNCTION__ . ': ' . $error[2]);
        }
        $this->affRows = $res;
    }

    /**
     * Return the number of fetched rows of the last "SELECT" statement.
     *
     * @return int  The number of fetched rows
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
        return $this->db->lastInsertId();
    }

    /**
     * Start a transaction.
     *
     * @return bool  Was the operation successful?
     */
    protected function startTransactionImpl(): bool
    {
        return $this->db->beginTransaction();
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
        return $this->db->rollBack();
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
        $quoted = $this->db->quote($string, \PDO::PARAM_STR);
        if ($quoted === false) {
            return $string;
        }
        // PDO::quote() also adds quotes around the string, so we have to remove them here
        return substr($quoted, 1, -1);
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

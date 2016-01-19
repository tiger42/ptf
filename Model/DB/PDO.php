<?php

namespace Ptf\Model\DB;

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
     * @var integer
     */
    protected $numRows;

    /**
     * The number of affected rows after the last "INSERT", "UPDATE" or "DELETE" statement
     * @var integer
     */
    protected $affRows;

    /**
     * Disconnect from the database
     */
    public function __destruct()
    {
        unset($this->db);
    }

    /**
     * Perform a "SELECT" query on the database
     *
     * @param   string $query                The SQL query string
     * @return  integer                      The number of fetched rows
     * @throws  \Ptf\Core\Exception\DBQuery  If the query has failed
     */
    protected function runQuery($query)
    {
        $statement = $this->db->query($query);
        if ($statement === false) {
            $error = $this->db->errorInfo();
            $this->errLogger->logSys(get_class($this) . "::" . __FUNCTION__, $error[2], \Ptf\Util\Logger::ERROR);
            throw new \Ptf\Core\Exception\DBQuery(get_class($this) . "::" . __FUNCTION__ . ": " . $error[2]);
        }
        $this->statement = $statement;
        $this->numRows   = $statement->rowCount();

        return $this->numRows;
    }

    /**
     * Fetch a row from the query result, advance the row pointer
     *
     * @return  mixed                       Result row as assoc array; FALSE, if result has no more rows
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
     * Perform a manipulation query on the database (e.g. "UPDATE", "INSERT")
     *
     * @param   string $sql                  The SQL statement to execute
     * @throws  \Ptf\Core\Exception\DBQuery  If the query has failed
     */
    protected function execSqlImpl($sql)
    {
        $res = $this->db->exec($sql);

        if ($res === false) {
            $error = $this->db->errorInfo();
            $this->errLogger->logSys(get_class($this) . "::" . __FUNCTION__, $error[2], \Ptf\Util\Logger::ERROR);
            throw new \Ptf\Core\Exception\DBQuery(get_class($this) . "::" . __FUNCTION__ . ": " . $error[2]);
        }
        $this->affRows = $res;
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
        return $this->db->lastInsertId();
    }

    /**
     * Start a transaction
     *
     * @return  boolean                     Was the operation successful?
     */
    protected function startTransactionImpl()
    {
        return $this->db->beginTransaction();
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
        return $this->db->rollBack();
    }

    /**
     * Escape a string to be safely used in database queries
     *
     * @param   string $string              The string to be escaped
     * @return  string                      The escaped string
     */
    public function escapeString($string)
    {
        $quoted = $this->db->quote($string, \PDO::PARAM_STR);
        if ($quoted === false) {
            return $string;
        }
        // PDO::quote() also adds quotes around the string, so we have to remove them here
        return substr($quoted, 1, -1);
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

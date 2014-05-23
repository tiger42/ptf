<?php

namespace Ptf\Model\DB\PDO;

/**
 * Database wrapper for PDO (MySQL driver)
 */
class MySQL extends \Ptf\Model\DB\PDO
{
    /**
     * Connect to the database
     *
     * @throws  \Ptf\Core\Exception\DBConnect If the DB connection could not be established
     */
    protected function connect()
    {
        $dsn = 'mysql:dbname=' . $this->config->getDatabase() . ';host=' . $this->config->getHost()
            . ';port=' . $this->config->getPort() . ';charset=' . $this->config->getCharset();

        try {
            $this->db = new \PDO($dsn, $this->config->getUsername(), $this->config->getPassword());
        } catch (\PDOException $e) {
            $this->errLogger->logSys(get_class($this) . "::" . __FUNCTION__, $e->getMessage(), \Ptf\Util\Logger::ERROR);
            throw new \Ptf\Core\Exception\DBConnect(get_class($this) . "::" . __FUNCTION__ . ":" . $e->getMessage());
        }
    }

    /**
     * Perform a "SELECT" query on the database
     *
     * @param   string $query               The SQL query string
     * @param   integer $offset             Offset of the first row
     * @param   integer $rowCount           Number of rows to fetch
     * @return  integer                     The number of fetched rows
     */
    protected function queryImpl($query, $offset = 0, $rowCount = null)
    {
        $limit = '';
        if ($rowCount !== null) {
            $limit = ' LIMIT ' . (int)$offset . ', ' . (int)$rowCount;
        } else if ($offset > 0) {
            $limit = ' LIMIT ' . (int)$offset . ', 18446744073709551615';
        }
        return $this->runQuery($query . $limit);
    }

    /**
     * Return all column names of the given table
     *
     * @param   string $tableName           The table to determine the column names of
     * @return  string[]                    The names of the table's columns
     */
    public function getColumnNames($tableName)
    {
        $this->query('DESCRIBE ' . $this->quoteIdentifier($tableName));
        $columns = [];
        while (($row = $this->fetch())) {
            $columns[] = $row['field'];
        }
        return $columns;
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

}

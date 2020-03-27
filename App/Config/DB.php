<?php

namespace Ptf\App\Config;

/**
 * Configuration for DB class.
 */
class DB extends \Ptf\App\Config
{
    /**
     * Initialize the configuration data.
     */
    public function __construct()
    {
        $this->configData = [
            'driver'   => 'MySQLi',
            'port'     => '3306',
            'host'     => 'localhost',
            'socket'   => '',
            'username' => null,
            'password' => null,
            'database' => null,
            'charset'  => 'utf8',
        ];
    }

    /**
     * Get the DB driver name setting.
     *
     * @return string  The configured name of the DB driver
     */
    public function getDriver()
    {
        return $this->driver;
    }

    /**
     * Get the DB port setting.
     *
     * @return string  The configured DB port
     */
    public function getPort()
    {
        return $this->port;
    }

    /**
     * Get the DB host name setting.
     *
     * @return string  The configured DB host name
     */
    public function getHost()
    {
        return $this->host;
    }

    /**
     * Get the DB socket setting.
     *
     * @return string  The configured DB socket
     */
    public function getSocket()
    {
        return $this->socket;
    }

    /**
     * Get the username setting.
     *
     * @return string  The configured username
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * Get the password setting.
     *
     * @return string  The configured password
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Get the the database name setting.
     *
     * @return string  The configured database name
     */
    public function getDatabase()
    {
        return $this->database;
    }

    /**
     * Get the database character set setting.
     *
     * @return string  The configured character set
     */
    public function getCharset()
    {
        return $this->charset;
    }
}

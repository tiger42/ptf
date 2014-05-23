<?php

namespace Ptf\App\Config;

/**
 * Configuration for Auth\DB class
 */
class AuthDB extends \Ptf\App\Config\Auth
{
    /**
     * Initialize the configuration data
     */
    public function __construct()
    {
        parent::__construct();

        $this->configData = array_merge($this->configData, [
            'connection'    => null,
            'table'         => null,
            'col_username'  => null,
            'col_password'  => null,
            'col_user_id'   => '',
            'col_is_active' => ''
        ]);
    }

    /**
     * Get the DB connection name setting
     *
     * @return  string                      The configured name of the DB connection
     */
    public function getConnection()
    {
        return $this->connection;
    }

    /**
     * Get the DB table name setting
     *
     * @return  string                      The configured name of the DB table
     */
    public function getTable()
    {
        return $this->table;
    }

    /**
     * Get the "username" column setting
     *
     * @return  string                      The configured name of the "username" column
     */
    public function getColUsername()
    {
        return $this->col_username;
    }

    /**
     * Get the "password" column setting
     *
     * @return  string                      The configured name of the "password" column
     */
    public function getColPassword()
    {
        return $this->col_password;
    }

    /**
     * Get the "user_id" column setting
     *
     * @return  string                      The configured name of the "user_id" column
     */
    public function getColUserId()
    {
        return $this->col_user_id;
    }

    /**
     * Get the "is_active" column setting
     *
     * @return  string                      The configured name of the "is_active" column
     */
    public function getColIsActive()
    {
        return $this->col_is_active;
    }

}

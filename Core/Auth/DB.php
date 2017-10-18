<?php

namespace Ptf\Core\Auth;

/**
 * User authentication for user data stored in DB.
 */
class DB extends \Ptf\Core\Auth
{
    /**
     * Return the user ID of the current user from the DB table.
     *
     * @return mixed  The current user ID
     */
    public function getUserId()
    {
        return $this->session->authData['userid'];
    }

    /**
     * Log the user in.
     *
     * @param string $username  The username to check
     * @param string $password  The password to check
     *
     * @return bool  Was the login successful?
     */
    protected function loginImpl(string $username, string $password): bool
    {
        $dbConfig = $this->context->getConfig($this->config->getConnection());
        $dbTable  = new \Ptf\Model\DB\Table($this->config->getTable(), $dbConfig, $this->context);

        $username = str_replace(['"', '\'', '`'], '', $username);
        $dbTable->{$this->config->getColUsername()} = $username;
        $dbTable->setCompareMode($this->config->getColUsername(), \Ptf\Model\DB\Table::COMP_CI);
        if (strlen($this->config->getColIsActive())) {
            $dbTable->{$this->config->getColIsActive()} = 1;
        }
        if ($dbTable->fetch()
            && $this->checkPassword($password, $dbTable->{$this->config->getColPassword()})
        ) {
            $data = $this->session->authData;
            if (strlen($this->config->getColUserId())) {
                $data['userid'] = $dbTable->{$this->config->getColUserId()};
            }
            $this->session->authData = $data;

            return true;
        }
        return false;
    }

    /**
     * Test the user's password against the hash value (SHA1) from the DB.<br />
     * (overwrite in child class if other hashing algorithm should be used).
     *
     * @param string $password  The password to test against the hash
     * @param string $hash      The SHA1 hash value from the DB
     *
     * @return bool  Does the password match?
     */
    protected function checkPassword(string $password, string $hash): bool
    {
        return sha1($password) == $hash;
    }

    /**
     * Log the user out.
     */
    protected function logoutImpl(): void
    {
        // Nothing to do here...
    }
}

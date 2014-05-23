<?php

namespace Ptf\Core;

/**
 * Abstract user authentication class
 */
abstract class Auth
{
    use \Ptf\Traits\Singleton;

    /**
     * The configuration object
     * @var \Ptf\App\Config\Auth
     */
    protected $config;
    /**
     * The session object
     * @var \Ptf\Core\Session
     */
    protected $session;
    /**
     * The application's context;
     * @var \Ptf\App\Context
     */
    protected $context;

    /**
     * Initialize the Auth object, start the session
     *
     * @param   \Ptf\App\Config\Auth $config The configuration to initialize with
     * @param   \Ptf\Core\Session $session   The application's session object
     * @param   \Ptf\App\Context $context    The application's context
     */
    public function init(\Ptf\App\Config\Auth $config, \Ptf\Core\Session $session, \Ptf\App\Context $context)
    {
        $this->config  = $config;
        $this->session = $session;
        $this->context = $context;

        $this->session->start();
        if (!is_array($this->session->authData)) {
            $this->session->authData = [];
        }
    }

    /**
     * Check user authentication
     *
     * @return  boolean                     Is the user authenticated?
     */
    final public function checkAuth()
    {
        $data = $this->session->authData;

        if (is_array($data)
            && isset($data['username'])
            && isset($data['expiry'])
            && isset($data['checkhash'])
            && md5($data['username'] . $data['expiry'] . $this->config->getSalt()) == $data['checkhash']
            && time() <= $data['expiry'])
        {
            // Refresh the expiry time
            $data['expiry']    = time() + $this->config->getIdletime();
            $data['checkhash'] = md5($data['username'] . $data['expiry'] . $this->config->getSalt());
            $this->session->authData = $data;
            return true;
        }
        $this->logout();

        return false;
    }

    /**
     * Return the login name of the user
     *
     * @return  string                      The username
     */
    public function getUsername()
    {
        return $this->session->authData['username'];
    }

    /**
     * Log the user in
     *
     * @param   string $username            The username to check
     * @param   string $password            The password to check
     * @return  boolean                     Was the login successful?
     */
    final public function login($username, $password)
    {
        if (!$this->loginImpl($username, $password)) {
            $this->context->getLogger()->logSys(get_class($this) . "::" . __FUNCTION__, "Login failed for user '" . $username . "'", \Ptf\Util\Logger::INFO);
            $this->logout();

            return false;
        }
        $this->context->getLogger()->logSys(get_class($this) . "::" . __FUNCTION__, "User '" . $username . "' has logged in", \Ptf\Util\Logger::INFO);

        $data = $this->session->authData;
        $data['username']  = $username;
        $data['expiry']    = time() + $this->config->getIdletime();
        $data['checkhash'] = md5($username . $data['expiry'] . $this->config->getSalt());
        $this->session->authData = $data;

        return true;
    }

    /**
     * Log the user in.<br />
     * (to be implemented by child classes)
     *
     * @param   string $username            The username to check
     * @param   string $password            The password to check
     * @return  boolean                     Was the login successful?
     */
    abstract protected function loginImpl($username, $password);

    /**
     * Log the user out and clear the auth session data
     */
    final public function logout()
    {
        $this->logoutImpl();
        if (isset($this->session->authData['username'])) {
            $this->context->getLogger()->logSys(get_class($this) . "::" . __FUNCTION__, "User '" . $this->session->authData['username'] . "' has logged out", \Ptf\Util\Logger::INFO);
        }
        $this->session->authData = [];
    }

    /**
     * Log the user out.<br />
     * (to be implemented by child classes)
     */
    abstract protected function logoutImpl();

}

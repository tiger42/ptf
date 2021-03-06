<?php

namespace Ptf\Core;

/**
 * Abstract user authentication class.
 */
abstract class Auth
{
    use \Ptf\Traits\Singleton;

    /** @var \Ptf\App\Config\Auth  The configuration object */
    protected $config;

    /** @var \Ptf\Core\Session  The session object */
    protected $session;

    /** @var \Ptf\App\Context  The application's context */
    protected $context;

    /**
     * Initialize the Auth object, start the session.
     *
     * @param \Ptf\App\Config\Auth $config   The configuration to initialize with
     * @param \Ptf\Core\Session    $session  The application's session object
     * @param \Ptf\App\Context     $context  The application's context
     */
    public function init(\Ptf\App\Config\Auth $config, \Ptf\Core\Session $session, \Ptf\App\Context $context): void
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
     * Check the user authentication and optionally refresh the expiry time.
     *
     * @param bool $refresh  Refresh the expiry time?
     *
     * @return bool  Is the user authenticated?
     */
    final public function checkAuth(bool $refresh = true): bool
    {
        $data = $this->session->authData;

        if (is_array($data)
            && isset($data['username'])
            && isset($data['expiry'])
            && isset($data['checkhash'])
            && md5($data['username'] . $data['expiry'] . $this->config->getSalt()) == $data['checkhash']
            && time() <= $data['expiry']
        ) {
            // Refresh the expiry time
            if ($refresh) {
                $data['expiry']    = time() + $this->config->getIdletime();
                $data['checkhash'] = md5($data['username'] . $data['expiry'] . $this->config->getSalt());
                $this->session->authData = $data;
            }

            return true;
        }
        $this->logout();

        return false;
    }

    /**
     * Return the login name of the user.
     *
     * @return string  The username
     */
    public function getUsername(): string
    {
        return $this->session->authData['username'];
    }

    /**
     * Log the user in.
     *
     * @param string $username  The username to check
     * @param string $password  The password to check
     *
     * @return bool  Was the login successful?
     */
    final public function login(string $username, string $password): bool
    {
        if (!$this->loginImpl($username, $password)) {
            $this->context->getLogger()->logSys(
                get_class($this) . '::' . __FUNCTION__,
                'Login failed for user "' . $username . '"',
                \Ptf\Util\Logger::INFO
            );
            $this->logout();

            return false;
        }
        $this->context->getLogger()->logSys(
            get_class($this) . '::' . __FUNCTION__,
            'User "' . $username . '" has logged in',
            \Ptf\Util\Logger::INFO
        );

        $data = $this->session->authData;
        $data['username']  = $username;
        $data['expiry']    = time() + $this->config->getIdletime();
        $data['checkhash'] = md5($username . $data['expiry'] . $this->config->getSalt());
        $this->session->authData = $data;

        return true;
    }

    /**
     * Log the user in.<br />
     * (to be implemented by child classes).
     *
     * @param string $username  The username to check
     * @param string $password  The password to check
     *
     * @return bool  Was the login successful?
     */
    abstract protected function loginImpl(string $username, string $password): bool;

    /**
     * Log the user out and clear the auth session data.
     */
    final public function logout(): void
    {
        $this->logoutImpl();
        if (isset($this->session->authData['username'])) {
            $this->context->getLogger()->logSys(
                get_class($this) . '::' . __FUNCTION__,
                'User "' . $this->session->authData['username'] . '" has logged out',
                \Ptf\Util\Logger::INFO
            );
        }
        $this->session->authData = [];
    }

    /**
     * Log the user out.<br />
     * (to be implemented by child classes).
     */
    abstract protected function logoutImpl(): void;
}

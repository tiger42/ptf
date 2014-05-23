<?php

namespace Ptf\Core;

/**
 * Abstract session wrapper class
 */
abstract class Session
{
    use \Ptf\Traits\Singleton;

    /**
     * The configuration object
     * @var \Ptf\App\Config\Session
     */
    protected $config;
    /**
     * The application's context
     * @var \Ptf\App\Context
     */
    protected $context;
    /**
     * The session path
     * @var string
     */
    protected $path;
    /**
     * The name of the session
     * @var string
     */
    protected $sessionName;


    /**
     * Initialize the session object
     *
     * @param   \Ptf\App\Config\Session $config The configuration to initialize with
     * @param   \Ptf\App\Context $context       The application's context
     */
    public function init(\Ptf\App\Config\Session $config, \Ptf\App\Context $context)
    {
        $this->config  = $config;
        $this->context = $context;

        $sessionName = $config->getSessionName();
        if (strlen($sessionName)) {
            $this->setSessionName($sessionName);
        }
        $maxLifetime = $config->getMaxLifetime();
        if (is_numeric($maxLifetime)) {
            $this->setMaxLifetime($maxLifetime);
        }
    }

    /**
     * Get the value of the session variable with the given name.<br />
     * (magic getter function)
     *
     * @param   string $name                Name of the session variable to get the value of
     * @return  mixed                       The value of the session variable
     */
    public function __get($name)
    {
        return isset($_SESSION[$name]) ? $_SESSION[$name] : null;
    }

    /**
     * Set the value of the session variable with the given name.<br />
     * (magic setter function)
     *
     * @param   string $name                Name of the session variable to set the value of
     * @param   mixed $value                The value to set
     */
    public function __set($name, $value)
    {
        $_SESSION[$name] = $value;
    }

    /**
     * Check if the session variable with the given name is set.<br />
     * (magic isset function)
     *
     * @param   string $name                Name of the session variable to check
     * @return  boolean                     Is the session variable set?
     */
    public function __isset($name)
    {
        return isset($_SESSION[$name]);
    }

    /**
     * Unset the given session variable.<br />
     * (magic unset function)
     *
     * @param   string $name                Name of the session variable to unset
     */
    public function __unset($name)
    {
        unset($_SESSION[$name]);
    }

    /**
     * Register the session handler functions and start the session
     *
     * @return  boolean                     Was the session started successfully?
     */
    final public function start()
    {
        if ($this->isStarted()) {
            return true;
        }
        session_set_save_handler(
            [$this, 'openSession'],
            [$this, 'closeSession'],
            [$this, 'readSession'],
            [$this, 'writeSession'],
            [$this, 'destroySession'],
            [$this, 'gcSession']
        );
        @session_start();
        if (!$this->isStarted()) {
            $this->context->getLogger('error')->logSys(get_class($this) . "::" . __FUNCTION__, "Unable to start session", \Ptf\Util\Logger::WARN);
            return false;
        }
        $this->context->getLogger()->logSys(get_class($this) . "::" . __FUNCTION__, "Session '" . $this->getSessionId() . "' started");
        return true;
    }

    /**
     * Return whether the session has been started
     *
     * @return  boolean                     Is the session running?
     */
    final public function isStarted()
    {
        return strlen(session_id()) > 0;
    }

    /**
     * Return the current maximum session lifetime
     *
     * @return  integer                     The maximum session lifetime in seconds
     */
    final public function getMaxLifetime()
    {
        return (int)ini_get('session.gc_maxlifetime');
    }

    /**
     * Set the maximum session lifetime.<br />
     * Must be called before start()!
     *
     * @param   integer $seconds            The max lifetime to set [sec]
     */
    final public function setMaxLifetime($maxLifetime)
    {
        init_set('session.gc_maxlifetime', $maxLifetime);
    }

    /**
     * Return the current session ID
     *
     * @return  string                      The current session ID
     */
    final public function getSessionId()
    {
        return session_id();
    }

    /**
     * Set the session ID.<br />
     * Must be called before start()!
     *
     * @param   string $id                  The session ID to set
     */
    final public function setSessionId($id)
    {
        session_id($id);
    }

    /**
     * Return the current session name
     *
     * @return  string                      The current session name
     */
    final public function getSessionName()
    {
        return session_name();
    }

    /**
     * Set the session name.<br />
     * Must be called before start()!
     *
     * @param   string $name                The session name to set
     */
    final public function setSessionName($name)
    {
        session_name($name);
    }

    /**
     * Destroy the session
     */
    final public function destroy()
    {
        $_SESSION = [];
        session_destroy();
    }

    /**
     * Open the session (handler function)
     *
     * @param   string $path                The session save path
     * @param   string $sessionName         The name of the session
     * @return  boolean                     Was the operation successful?
     */
    public function openSession($path, $sessionName)
    {
        $this->path = $path;
        $this->sessionName = $sessionName;

        return true;
    }

    /**
     * Close the session (handler function)
     *
     * @return  boolean                     Was the operation successful?
     */
    public function closeSession()
    {
        return true;
    }

    /**
     * Read the session data (handler function)
     *
     * @param   string $id                  The current session ID
     * @return  string                      The read session data
     */
    abstract public function readSession($id);

    /**
     * Write the session data (handler function)
     *
     * @param   string $id                  The current session ID
     * @param   string $data                The session data to write
     * @return  boolean                     Was the operation successful?
     */
    abstract public function writeSession($id, $data);

    /**
     * Kill the session (handler function)
     *
     * @param   string $id                  The current session ID
     * @return  boolean                     Was the operation successful?
     */
    abstract public function destroySession($id);

    /**
     * Perform a garbage collection (handler function)
     *
     * @param   integer $maxLifetime        The maximum session lifetime [sec]
     * @return  boolean                     Was the operation successful?
     */
    abstract public function gcSession($maxLifetime);

}

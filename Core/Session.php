<?php

namespace Ptf\Core;

/**
 * Abstract session wrapper class.
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
     * Initialize the session object, must be called before start().
     *
     * @param \Ptf\App\Config\Session $config   The configuration to initialize with
     * @param \Ptf\App\Context        $context  The application's context
     */
    public function init(\Ptf\App\Config\Session $config, \Ptf\App\Context $context): void
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
     * (magic getter function).
     *
     * @param string $name  Name of the session variable to get the value of
     *
     * @return mixed  The value of the session variable
     */
    public function __get(string $name)
    {
        return $_SESSION[$name] ?? null;
    }

    /**
     * Set the value of the session variable with the given name.<br />
     * (magic setter function).
     *
     * @param string $name   Name of the session variable to set the value of
     * @param mixed  $value  The value to set
     */
    public function __set(string $name, $value): void
    {
        $_SESSION[$name] = $value;
    }

    /**
     * Check if the session variable with the given name is set.<br />
     * (magic isset function).
     *
     * @param string $name  Name of the session variable to check
     *
     * @return bool  Is the session variable set?
     */
    public function __isset(string $name): bool
    {
        return isset($_SESSION[$name]);
    }

    /**
     * Unset the given session variable.<br />
     * (magic unset function).
     *
     * @param string $name  Name of the session variable to unset
     */
    public function __unset(string $name): void
    {
        unset($_SESSION[$name]);
    }

    /**
     * Register the session handler functions and start the session.
     *
     * @return bool  Was the session started successfully?
     */
    final public function start(): bool
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
            $this->context->getLogger('error')->logSys(
                get_class($this) . "::" . __FUNCTION__,
                "Unable to start session",
                \Ptf\Util\Logger::WARN
            );
            return false;
        }
        $this->context->getLogger()->logSys(
            get_class($this) . "::" . __FUNCTION__,
            "Session '" . $this->getSessionId() . "' started"
        );
        return true;
    }

    /**
     * Return whether the session has been started.
     *
     * @return bool  Is the session running?
     */
    final public function isStarted(): bool
    {
        return strlen(session_id()) > 0;
    }

    /**
     * Return the current maximum session lifetime.
     *
     * @return int  The maximum session lifetime in seconds
     */
    final public function getMaxLifetime(): int
    {
        return (int)ini_get('session.gc_maxlifetime');
    }

    /**
     * Set the maximum session lifetime.<br />
     * Must be called before start()!
     *
     * @param int $seconds  The max lifetime to set [sec]
     */
    final public function setMaxLifetime(int $maxLifetime): void
    {
        init_set('session.gc_maxlifetime', $maxLifetime);
    }

    /**
     * Return the current session ID.
     *
     * @return string  The current session ID
     */
    final public function getSessionId(): string
    {
        return session_id();
    }

    /**
     * Set the session ID.<br />
     * Must be called before start()!
     *
     * @param string $id  The session ID to set
     */
    final public function setSessionId(string $id): void
    {
        session_id($id);
    }

    /**
     * Return the current session name.
     *
     * @return string  The current session name
     */
    final public function getSessionName(): string
    {
        return session_name();
    }

    /**
     * Set the session name.<br />
     * Must be called before start()!
     *
     * @param string $name  The session name to set
     */
    final public function setSessionName(string $name): void
    {
        session_name($name);
    }

    /**
     * Destroy the session.
     */
    final public function destroy(): void
    {
        $_SESSION = [];
        session_destroy();
    }

    /**
     * Open the session (handler function).
     *
     * @param string $path         The session save path
     * @param string $sessionName  The name of the session
     *
     * @return bool  Was the operation successful?
     */
    public function openSession(string $path, string $sessionName): bool
    {
        $this->path = $path;
        $this->sessionName = $sessionName;

        return true;
    }

    /**
     * Close the session (handler function).
     *
     * @return bool  Was the operation successful?
     */
    public function closeSession(): bool
    {
        return true;
    }

    /**
     * Read the session data (handler function).
     *
     * @param string $id  The current session ID
     *
     * @return string  The read session data
     */
    abstract public function readSession(string $id): string;

    /**
     * Write the session data (handler function).
     *
     * @param string $id    The current session ID
     * @param string $data  The session data to write
     *
     * @return bool  Was the operation successful?
     */
    abstract public function writeSession(string $id, string $data): bool;

    /**
     * Kill the session (handler function).
     *
     * @param string $id  The current session ID
     *
     * @return bool  Was the operation successful?
     */
    abstract public function destroySession(string $id): bool;

    /**
     * Perform a garbage collection (handler function).
     *
     * @param int $maxLifetime  The maximum session lifetime [sec]
     *
     * @return bool  Was the operation successful?
     */
    abstract public function gcSession(int $maxLifetime): bool;
}

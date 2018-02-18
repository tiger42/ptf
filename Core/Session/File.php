<?php

namespace Ptf\Core\Session;

/**
 * File based session class.
 */
class File extends \Ptf\Core\Session
{
    /**
     * Initialize the File session, must be called before start().
     *
     * @param \Ptf\App\Config\SessionFile $config   The configuration to initialize with
     * @param \Ptf\App\Context            $context  The application's context
     *
     * @throws \InvalidArgumentException  If the Config object has the wrong type
     * @throws \RuntimeException          If the session save path is not writable
     */
    public function init(\Ptf\App\Config\Session $config, \Ptf\App\Context $context): void
    {
        if (!($config instanceof \Ptf\App\Config\SessionFile)) {
            throw new \InvalidArgumentException(get_class($this) . '::' . __FUNCTION__
                . ': $config must be instance of class \\Ptf\\App\\Config\\SessionFile');
        }

        parent::init($config, $context);

        if (!is_writable(session_save_path())) {
            throw new \RuntimeException(get_class($this) . '::' . __FUNCTION__
                . ': Session save path is not writable: ' . session_save_path());
        }
    }

    /**
     * Read the session data (handler function).
     *
     * @param string $id  The current session ID
     *
     * @return string  The read session data
     */
    public function readSession(string $id): string
    {
        return (string)@file_get_contents($this->path . '/sess_' . $id);
    }

    /**
     * Write the session data (handler function).
     *
     * @param string $id    The current session ID
     * @param string $data  The session data to write
     *
     * @return bool  Was the operation successful?
     */
    public function writeSession(string $id, string $data): bool
    {
        return (bool)@file_put_contents($this->path . '/sess_' . $id, $data, LOCK_EX);
    }

    /**
     * Kill the session (handler function).
     *
     * @param string $id  The current session ID
     *
     * @return bool  Was the operation successful?
     */
    public function destroySession(string $id): bool
    {
        return @unlink($this->path . '/sess_' . $id);
    }

    /**
     * Perform a garbage collection (handler function).
     *
     * @param int $maxLifetime  The maximum session lifetime [sec]
     *
     * @return bool  Was the operation successful?
     */
    public function gcSession(int $maxLifetime): bool
    {
        foreach (glob($this->path . '/sess_*') as $filename) {
            if (filemtime($filename) + $maxLifetime < time()) {
                @unlink($filename);
            }
        }
        return true;
    }
}

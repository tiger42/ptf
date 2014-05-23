<?php

namespace Ptf\Core\Session;

/**
 * File based session class
 *
 * @package     Data\Session
 */
class File extends \Ptf\Core\Session
{
    /**
     * Initialize the member variables
     *
     * @param   \Ptf\App\Config\SessionFile $config The configuration to initialize with
     * @param   \Ptf\App\Context $context           The application's context
     * @throws  \RuntimeException                   If the session save path is not writable
     */
    public function init(\Ptf\App\Config\SessionFile $config, \Ptf\App\Context $context)
    {
        parent::init($config, $context);

        if (!is_writable(session_save_path())) {
            throw new \RuntimeException(get_class($this) . "::" . __FUNCTION__ . ": Session save path is not writable: " . session_save_path());
        }
    }

    /**
     * Read the session data (handler function)
     *
     * @param   string $id                  The current session ID
     * @return  string                      The read session data
     */
    public function readSession($id)
    {
        return (string)@file_get_contents($this->path . '/sess_' . $id);
    }

    /**
     * Write the session data (handler function)
     *
     * @param   string $id                  The current session ID
     * @param   string $data                The session data to write
     * @return  boolean                     Was the operation successful?
     */
    public function writeSession($id, $data)
    {
        return (boolean)@file_put_contents($this->path . '/sess_' . $id, $data, LOCK_EX);
    }

    /**
     * Kill the session (handler function)
     *
     * @param   string $id                  The current session ID
     * @return  boolean                     Was the operation successful?
     */
    public function destroySession($id)
    {
        return @unlink($this->path . '/sess_' . $id);
    }

    /**
     * Perform a garbage collection (handler function)
     *
     * @param   integer $maxLifetime        The maximum session lifetime [sec]
     * @return  boolean                     Was the operation successful?
     */
    public function gcSession($maxLifetime)
    {
        foreach (glob($this->path . '/sess_*') as $filename) {
            if (filemtime($filename) + $maxLifetime < time()) {
                @unlink($filename);
            }
        }
        return true;
    }

}

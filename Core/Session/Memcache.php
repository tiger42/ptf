<?php

namespace Ptf\Core\Session;

/**
 * Memcache based session class
 */
class Memcache extends \Ptf\Core\Session
{
    /**
     * Internal Memcache object
     * @var Memcache
     */
    protected $memcache;
    /**
     * List of Memcache hosts
     * @var string[]
     */
    protected $hosts;

    /**
     * Initialize the Memcache session
     *
     * @param   \Ptf\App\Config\SessionMemcache $config The configuration to initialize with
     * @param   \Ptf\App\Context $context               The application's context
     */
    public function init(\Ptf\App\Config\SessionMemcache $config, \Ptf\App\Context $context)
    {
        parent::init($config, $context);

        $this->hosts    = $config->getHosts();
        $this->memcache = new \Memcached();

        $this->connect();
    }

    /**
     * Disconnect from Memcache service
     */
    public function __destruct()
    {
        $this->memcache->quit();
    }

    /**
     * Read the session data (handler function)
     *
     * @param   string $id                  The current session ID
     * @return  string                      The read session data
     */
    public function readSession($id)
    {
        return (string)$this->memcache->get($this->path . '/sess_' . $id);
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
        $lifetime = time() + $this->getMaxLifetime();
        $this->connect();

        return $this->memcache->set($this->path . '/sess_' . $id, $data, $lifetime);
    }

    /**
     * Kill the session (handler function)
     *
     * @param   string $id                  The current session ID
     * @return  boolean                     Was the operation successful?
     */
    public function destroySession($id)
    {
        return $this->memcache->delete($this->path . '/sess_' . $id);
    }

    /**
     * Perform a garbage collection (handler function)
     *
     * @param   integer $maxLifetime        The maximum session lifetime [sec]
     * @return  boolean                     Was the operation successful?
     */
    public function gcSession($maxLifetime)
    {
        return true;   // Memcache automatically deletes old data after their lifetime has expired
    }

    /**
     * Connect to the Memcache service
     */
    protected function connect()
    {
        foreach ($this->hosts as $hostPort) {
            $host = explode(':', $hostPort);
            $this->memcache->addServer($host[0], $host[1]);
        }
    }

}

<?php

namespace Ptf\App;

/**
 * Abstract configuration settings
 */
abstract class Config implements \ArrayAccess
{
    use \Ptf\Traits\ArrayAccess;

    /**
     * The configuration data
     * @var array
     */
    protected $configData;

    /**
     * Initialize the configuration data
     */
    protected function __construct()
    {
        $this->configData = [];
    }

    /**
     * Get the given option's value.<br />
     * (magic getter function)
     *
     * @param   string $option              The configuration option to get the value of
     * @return  mixed                       The option's value
     * @throws \Ptf\Core\Exception\Config   If the requested configuration parameter is mandatory but not set
     */
    final public function __get($option)
    {
        if (array_key_exists($option, $this->configData) && $this->configData[$option] === null) {
            throw new \Ptf\Core\Exception\Config(get_class($this) . "::" . __FUNCTION__ . ": Option '" . $option . "' not configured");
        }
        return isset($this->configData[$option]) ? $this->configData[$option] : null;
    }

    /**
     * Set the given option's value.<br />
     * (magic setter function)
     *
     * @param   string $option              The configuration option to set
     * @param   mixed $value                The new value
     */
    final public function __set($option, $value)
    {
        $this->configData[$option] = $value;
    }

    /**
     * Determine whether the given configuration option is set.<br />
     * (magic isset function)
     *
     * @param   string $option              The configuration option to test
     * @return  boolean                     Is the configuration option set?
     */
    final public function __isset($option)
    {
        return isset($this->configData[$option]);
    }

    /**
     * Unset the given configuration option.<br />
     * (magic unset function)
     *
     * @param  string $option               The configuration option to unset
     */
    final public function __unset($option)
    {
        unset($this->configData[$option]);
    }

}

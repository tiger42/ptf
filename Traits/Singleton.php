<?php

namespace Ptf\Traits;

/**
 * Trait for classes that implement the Singleton pattern.
 */
trait Singleton
{
    /** @var Singleton  The Singleton instance of the class */
    protected static $instance;

    /**
     * Prevent the class from being instantiated directly.
     */
    protected function __construct()
    {
    }

    /**
     * Get the Singleton instance of the class.
     *
     * @return Singleton  The Singleton instance of the class
     */
    final public static function getInstance(): self
    {
        if (static::$instance === null) {
            static::$instance = new static();
        }
        return static::$instance;
    }

    /**
     * Prevent Singleton instance of the class from being cloned.
     *
     * @codeCoverageIgnore
     */
    private function __clone()
    {
    }

    /**
     * Prevent Singleton instance of the class from being unserialized.
     *
     * @throws \RuntimeException  If wakeup of object is attempted
     *
     * @codeCoverageIgnore
     */
    public function __wakeup()
    {
        throw new \RuntimeException(get_class($this) . '::' . __FUNCTION__ . ': __wakeup of Singleton object is not allowed');
    }
}

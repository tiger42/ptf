<?php

namespace Ptf\Traits;

/**
 * Trait for classes that implement the Singleton pattern.
 */
trait Singleton
{
    /**
     * The Singleton instance of the class.
     *
     * @var Singleton
     */
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
    final private function __clone()
    {
    }

    /**
     * Prevent Singleton instance of the class from being unserialized.
     *
     * @codeCoverageIgnore
     */
    final private function __wakeup()
    {
    }
}

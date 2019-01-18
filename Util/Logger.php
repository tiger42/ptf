<?php

namespace Ptf\Util;

use Ptf\App\Context;

/**
 * Abstract message logger.
 */
abstract class Logger
{
    /** Log level for debug messages */
    public const DEBUG = 0;
    /** Log level for informative messages */
    public const INFO  = 1;
    /** Log level for notices */
    public const NOTE  = 2;
    /** Log level for warning messages */
    public const WARN  = 3;
    /** Log level for error messages */
    public const ERROR = 4;
    /** Log level for fatal error messages */
    public const FATAL = 5;

    /**
     * Singleton instances of concrete Logger classes
     * @var Logger[]
     */
    protected static $instances = [];

    /**
     * The name of the current log
     * @var string
     */
    protected $logName;

    /**
     * The logger's configured log level limit
     * @var int
     */
    protected $logLevelLimit;

    /**
     * Cache for available log levels
     * @var array
     */
    private $logLevels;

    /**
     * The application's context
     * @var Context
     */
    protected $context;

    /**
     * Initialize the member variables.
     *
     * @param string  $logName  The name of the log to set
     * @param Context $context  The application's context
     */
    protected function __construct(string $logName, Context $context)
    {
        $this->logName = $logName;
        $this->logLevelLimit = self::INFO;
        $this->context = $context;
    }

    /**
     * Close the connection to the log.
     */
    public function __destruct()
    {
        $this->closeLog();
    }

    /**
     * Prevent all child classes' Singleton instances from being cloned.
     */
    final private function __clone()
    {
    }

    /**
     * Prevent all child classes' Singleton instances from being unserialized.
     */
    final private function __wakeup()
    {
    }

    /**
     * Get Singleton instance of current Logger class, depending on the log name.
     *
     * @param string  $logName        The name of the logger to get
     * @param Context $context        The application's context
     * @param int     $logLevelLimit  The log level limit to set
     *
     * @return Logger  Singleton instance of concrete Logger class
     */
    final public static function getInstance(string $logName, Context $context, int $logLevelLimit = null): self
    {
        $class = get_called_class();
        if (!isset(static::$instances[$class])) {
            static::$instances[$class] = [];
        }
        if (!isset(static::$instances[$class][$logName])) {
            static::$instances[$class][$logName] = new static($logName, $context);
        }
        if ($logLevelLimit !== null) {
            static::$instances[$class][$logName]->setLogLevelLimit($logLevelLimit);
        }

        return static::$instances[$class][$logName];
    }

    /**
     * Set the logger's log level limit.
     *
     * @param int $logLevelLimit  The log level limit to set
     */
    final public function setLogLevelLimit(int $logLevelLimit): void
    {
        $this->logLevelLimit = $logLevelLimit;
    }

    /**
     * Open a connection to the log.
     */
    abstract protected function openLog(): void;

    /**
     * Close the connection to the log
     */
    abstract protected function closeLog(): void;

    /**
     * Add a line to the log if the given log level is equal or higher than the set log level limit.
     *
     * @param string $message   The message to log
     * @param int    $logLevel  The log level of the message
     */
    final public function log(string $message, $logLevel = self::INFO): void
    {
        if ($logLevel < $this->logLevelLimit) {
            return;
        }

        $timestamp  = \Ptf\Util\now();
        $remoteAddr = $this->context->isCli() ? 'CLI' : $this->context->getRequest()->getRemoteAddr();

        $this->openLog();
        $this->logImpl($message, $logLevel, $timestamp, $remoteAddr);
        $this->closeLog();
    }

    /**
     * Add a line to the log.<br />
     * (to be implemented by child classes).
     *
     * @param string $message        The message to log
     * @param int    $logLevel       The log level of the message
     * @param string $timestamp      Timestamp of the message
     * @param string $remoteAddress  IP address of the client
     */
    abstract protected function logImpl(string $message, int $logLevel, string $timestamp, string $remoteAddress): void;

    /**
     * Log an internal system message.
     *
     * @param string $function  The calling function or method
     * @param string $message   The message to log
     * @param int    $logLevel  The log level of the message
     */
    public function logSys(string $function, string $message, $logLevel = self::DEBUG): void
    {
        try {
            $this->log('(' . $function . ') ' . $message, $logLevel);
        } catch (\Ptf\Core\Exception\Logger $e) {
        }
    }

    /**
     * Add a log entry with log level "DEBUG".
     *
     * @param string $message  The message to log
     */
    final public function debug(string $message): void
    {
        $this->log($message, self::DEBUG);
    }

    /**
     * Add a log entry with log level "INFO".
     *
     * @param string $message  The message to log
     */
    final public function info(string $message): void
    {
        $this->log($message, self::INFO);
    }

    /**
     * Add a log entry with log level "NOTE".
     *
     * @param string $message  The message to log
     */
    final public function note(string $message): void
    {
        $this->log($message, self::NOTE);
    }

    /**
     * Add a log entry with log level "WARN".
     *
     * @param string $message  The message to log
     */
    final public function warn(string $message): void
    {
        $this->log($message, self::WARN);
    }

    /**
     * Add a log entry with log level "ERROR".
     *
     * @param string $message  The message to log
     */
    final public function error(string $message): void
    {
        $this->log($message, self::ERROR);
    }

    /**
     * Add a log entry with log level "FATAL".
     *
     * @param string $message  The message to log
     */
    final public function fatal(string $message): void
    {
        $this->log($message, self::FATAL);
    }

    /**
     * Return the given log level as string.
     *
     * @param int $logLevel  The log level to translate
     *
     * @return string  The name of the log level
     */
    final protected function translateLogLevel(int $logLevel): string
    {
        if ($this->logLevels === null) {
            $reflection = new \ReflectionClass(__CLASS__);
            $constants = array_flip($reflection->getConstants());
            $this->logLevels = $constants;
        }

        return $this->logLevels[$logLevel];
    }
}

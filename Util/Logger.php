<?php

namespace Ptf\Util;

/**
 * Abstract message logger
 */
abstract class Logger
{
    /** Log level for debug messages */
    const DEBUG = 0;
    /** Log level for informative messages */
    const INFO  = 1;
    /** Log level for notices */
    const NOTE  = 2;
    /** Log level for warning messages */
    const WARN  = 3;
    /** Log level for error messages */
    const ERROR = 4;
    /** Log level for fatal error messages */
    const FATAL = 5;

    /**
     * Singleton instances of concrete Logger classes
     * @var array
     */
    protected static $instances = [];
    /**
     * The name of the current log
     * @var string
     */
    protected $logName;
    /**
     * The logger's configured log level limit
     * @var integer
     */
    protected $logLevelLimit;
    /**
     * Cache for available log levels
     * @var array
     */
    private $logLevels;
    /**
     * The application's context
     * @var \Ptf\App\Context
     */
    protected $context;

    /**
     * Initialize the member variables
     *
     * @param   string $logName             The name of the log to set
     * @param   \Ptf\App\Context $context   The application's context
     */
    protected function __construct($logName, \Ptf\App\Context $context)
    {
        $this->logName = $logName;
        $this->logLevelLimit = self::INFO;
        $this->context = $context;
    }

    /**
     * Close the connection to the log
     */
    public function __destruct()
    {
        $this->closeLog();
    }

    /**
     * Prevent all child classes' Singleton instances from being cloned
     */
    final private function __clone()
    {
    }

    /**
     * Prevent all child classes' Singleton instances from being unserialized
     */
    final private function __wakeup()
    {
    }

    /**
     * Get Singleton instance of current Logger class, depending on the log name
     *
     * @param   string $logName             The name of the logger to get
     * @param   \Ptf\App\Context $context   The application's context
     * @param   integer $logLevelLimit      The log level limit to set
     * @return  \Ptf\Util\Logger            Singleton instance of concrete Logger class
     */
    final public static function getInstance($logName, \Ptf\App\Context $context, $logLevelLimit = null)
    {
        if (!strlen($logName)) {
            return null;
        }

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
     * Set the logger's log level limit
     *
     * @param   integer $logLevelLimit      The log level limit to set
     */
    final public function setLogLevelLimit($logLevelLimit)
    {
        $this->logLevelLimit = $logLevelLimit;
    }

    /**
     * Open a connection to the log
     */
    abstract protected function openLog();

    /**
     * Close the connection to the log
     */
    abstract protected function closeLog();

    /**
     * Add a line to the log if the given log level is equal or higher than the set log level limit
     *
     * @param   string $message             The message to log
     * @param   integer $logLevel           The log level of the message
     */
    final public function log($message, $logLevel = self::INFO)
    {
        if ($logLevel >= $this->logLevelLimit) {
            $timestamp  = \Ptf\Util\now();
            $remoteAddr = $this->context->getRequest()->getRemoteAddr();

            $this->openLog();
            $this->logImpl($message, $logLevel, $timestamp, $remoteAddr);
            $this->closeLog();
        }
    }

    /**
     * Add a line to the log.<br />
     * (to be implemented by child classes)
     *
     * @param   string $message             The message to log
     * @param   integer $logLevel           The log level of the message
     * @param   string $timestamp           Timestamp of the message
     * @param   string $remoteAddress       IP address of the client
     */
    abstract protected function logImpl($message, $logLevel, $timestamp, $remoteAddress);

    /**
     * Log an internal system message
     *
     * @param   string $function            The calling function or method
     * @param   string $message             The message to log
     * @param   string $logLevel            The log level of the message
     */
    public function logSys($function, $message, $logLevel = self::DEBUG)
    {
        try {
            $this->log("(" . $function . ") " . $message, $logLevel);
        } catch (\Ptf\Exception\Logger $e) {
        }
    }

    /**
     * Add a log entry with log level "DEBUG"
     *
     * @param   string $message             The message to log
     */
    final public function debug($message)
    {
        $this->log($message, self::DEBUG);
    }

    /**
     * Add a log entry with log level "INFO"
     *
     * @param   string $message             The message to log
     */
    final public function info($message)
    {
        $this->log($message, self::INFO);
    }

    /**
     * Add a log entry with log level "NOTE"
     *
     * @param   string $message             The message to log
     */
    final public function note($message)
    {
        $this->log($message, self::NOTE);
    }

    /**
     * Add a log entry with log level "WARN"
     *
     * @param   string $message             The message to log
     */
    final public function warn($message)
    {
        $this->log($message, self::WARN);
    }

    /**
     * Add a log entry with log level "ERROR"
     *
     * @param   string $message             The message to log
     */
    final public function error($message)
    {
        $this->log($message, self::ERROR);
    }

    /**
     * Add a log entry with log level "FATAL"
     *
     * @param   string $message             The message to log
     */
    final public function fatal($message)
    {
        $this->log($message, self::FATAL);
    }

    /**
     * Return the given log level as string
     *
     * @param   integer $logLevel           The log level to translate
     * @return  string                      The name of the log level
     */
    final protected function translateLogLevel($logLevel)
    {
        if ($this->logLevels === null) {
            $reflection = new \ReflectionClass(__CLASS__);
            $constants = array_flip($reflection->getConstants());
            $this->logLevels = $constants;
        }
        return $this->logLevels[$logLevel];
    }

}

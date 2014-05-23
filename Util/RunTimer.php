<?php

namespace Ptf\Util;

/**
 * Timer for runtime measurement
 */
class RunTimer
{
    /**
     * Start time of the timer
     * @var float
     */
    protected static $startTime;

    /**
     * Start the timer
     */
    public static function start()
    {
        self::$startTime = microtime(true);
    }

    /**
     * Get the start time of the timer
     *
     * @return  float                       The start time of the timer [sec.µsec]
     */
    public static function getStartTime()
    {
        return self::$startTime;
    }

    /**
     * Get the current runtime
     *
     * @return  float                       The current runtime [sec.µsec]
     */
    public static function getRunTime()
    {
        return microtime(true) - self::$startTime;
    }

}

\Ptf\Util\RunTimer::start();

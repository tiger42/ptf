<?php

namespace Ptf\Util;

/**
 * Timer for runtime measurement.
 */
class RunTimer
{
    /** @var float  Start time of the timer */
    protected static $startTime;

    /**
     * Start the timer.
     */
    public static function start(): void
    {
        self::$startTime = microtime(true);
    }

    /**
     * Get the start time of the timer.
     *
     * @return float  The start time of the timer [sec.µsec]
     */
    public static function getStartTime(): float
    {
        return self::$startTime;
    }

    /**
     * Get the current runtime.
     *
     * @return float  The current runtime [sec.µsec]
     */
    public static function getRunTime(): float
    {
        return microtime(true) - self::$startTime;
    }
}

RunTimer::start();

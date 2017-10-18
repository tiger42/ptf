<?php

namespace PtfTest;

require_once 'Application.php';

class Application extends \Ptf\Application
{
    public static function run(): void
    {
        self::initAutoloader(\Ptf\Core\Autoloader::getInstance());
        self::getContext();   // Initialize the Context Singleton
    }

    public static function getContext(): \Ptf\App\Context
    {
        return \PtfTest\App\Context::getInstance();
    }

    protected static function initAutoloader(\Ptf\Core\Autoloader $autoloader): void
    {
        $autoloader->registerNamespace('PtfTest', 'tests/src');
    }
}

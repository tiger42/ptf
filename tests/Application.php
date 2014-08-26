<?php

namespace PtfTest;

require_once 'Application.php';

class Application extends \Ptf\Application
{
    public static function run()
    {
        self::initAutoloader(\Ptf\Core\Autoloader::getInstance());
        self::getContext();   // Initialize the Context Singleton
    }

    public static function getContext()
    {
        return \PtfTest\Context::getInstance();
    }

    protected static function initAutoloader(\Ptf\Core\Autoloader $autoloader)
    {
        $autoloader->registerNamespace('PtfTest', 'tests');
    }

}

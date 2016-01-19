<?php

namespace Ptf\App\Config;

class GeneralTest extends \PHPUnit_Framework_TestCase
{
    public function __construct()
    {
        $this->configData = [
         'log_level' => (string)\Ptf\Util\Logger::INFO
        ];
    }

    public function testGetLogLevel()
    {
        $config = new General();

        $this->assertSame('1', $config->getLogLevel());
    }
}

<?php

namespace Ptf\App\Config;

class GeneralTest extends \PHPUnit_Framework_TestCase
{
    public function testGetLogLevel()
    {
        $config = new General();

        $this->assertSame('1', $config->getLogLevel());
        $this->assertSame('var/log/system.log', $config->getSystemLog());
        $this->assertSame('var/log/error.log', $config->getErrorLog());
    }
}

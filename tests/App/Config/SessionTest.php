<?php

namespace Ptf\App\Config;

class SessionTest extends \PHPUnit_Framework_TestCase
{
    public function testGetSessionName()
    {
        $config = new Session();

        $this->assertSame('', $config->getSessionName());
    }

    public function testGetMaxLifetime()
    {
        $config = new Session();

        $this->assertSame('', $config->getMaxLifetime());
    }
}

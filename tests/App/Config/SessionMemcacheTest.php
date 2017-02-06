<?php

namespace Ptf\App\Config;

class SessionMemcacheTest extends \PHPUnit\Framework\TestCase
{
    public function testGetHosts()
    {
        $config = new SessionMemcache();

        $this->assertSame(['localhost:11211'], $config->getHosts());
    }
}

<?php

namespace Ptf\App\Config;

class AuthTest extends \PHPUnit_Framework_TestCase
{
    public function testGetIdletime()
    {
        $config = new Auth();

        $this->assertSame('1800', $config->getIdletime());
    }

    public function testGetSalt()
    {
        $config = new Auth();

        $this->assertSame('mysecretsalt', $config->getSalt());
    }
}

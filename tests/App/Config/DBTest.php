<?php

namespace Ptf\App\Config;

class DBTest extends \PHPUnit_Framework_TestCase
{
    public function testGetDriver()
    {
        $config = new DB();

        $this->assertSame('MySQLi', $config->getDriver());
    }

    public function testGetPort()
    {
        $config = new DB();

        $this->assertSame('3306', $config->getPort());
    }

    public function testGetHost()
    {
        $config = new DB();

        $this->assertSame('localhost', $config->getHost());
    }

    public function testGetUsername()
    {
        $config = new DB();

        $this->setExpectedException(
            '\\Ptf\\Core\\Exception\\Config',
            'Ptf\App\Config\DB::__get: Option \'username\' not configured');
        $config->getUsername();
    }

    public function testGetPassword()
    {
        $config = new DB();

        $this->setExpectedException(
            '\\Ptf\\Core\\Exception\\Config',
            'Ptf\App\Config\DB::__get: Option \'password\' not configured');
        $config->getPassword();
    }

    public function testGetDatabase()
    {
        $config = new DB();

        $this->setExpectedException(
            '\\Ptf\\Core\\Exception\\Config',
            'Ptf\App\Config\DB::__get: Option \'database\' not configured');
        $config->getDatabase();
    }

    public function testGetCharset()
    {
        $config = new DB();

        $this->assertSame('utf8', $config->getCharset());
    }
}

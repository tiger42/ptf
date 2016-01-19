<?php

namespace Ptf\App\Config;

class AuthDBTest extends \PHPUnit_Framework_TestCase
{
    public function testGetConnection()
    {
        $config = new AuthDB();

        $this->setExpectedException(
            '\\Ptf\\Core\\Exception\\Config',
            'Ptf\App\Config\AuthDB::__get: Option \'connection\' not configured');
        $config->getConnection();
    }

    public function testGetTable()
    {
        $config = new AuthDB();

        $this->setExpectedException(
            '\\Ptf\\Core\\Exception\\Config',
            'Ptf\App\Config\AuthDB::__get: Option \'table\' not configured');
        $config->getTable();
    }

    public function testGetColUsername()
    {
        $config = new AuthDB();

        $this->setExpectedException(
            '\\Ptf\\Core\\Exception\\Config',
            'Ptf\App\Config\AuthDB::__get: Option \'col_username\' not configured');
        $config->getColUsername();
    }

    public function testGetColPassword()
    {
        $config = new AuthDB();

        $this->setExpectedException(
            '\\Ptf\\Core\\Exception\\Config',
            'Ptf\App\Config\AuthDB::__get: Option \'col_password\' not configured');
        $config->getColPassword();
    }

    public function testGetColUserId()
    {
        $config = new AuthDB();

        $this->assertSame('', $config->getColUserId());
    }

    public function testGetColIsActive()
    {
        $config = new AuthDB();

        $this->assertSame('', $config->getColIsActive());
    }
}

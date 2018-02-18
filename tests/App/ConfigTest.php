<?php

namespace Ptf\App;

class ConfigTest extends \PHPUnit\Framework\TestCase
{
    public function testGet()
    {
        $config = new MyConfig();

        $this->assertCount(4, $config->getConfigData());
        $this->assertSame('test123', $config->testString);
        $this->assertSame('', $config->emptyString);
        $this->assertSame(['foo', 'bar'], $config->testArray);
        $this->assertNull($config->foobar);
    }

    public function testGetException()
    {
        $config = new MyConfig();

        $this->expectException('\\Ptf\\Core\\Exception\Config');
        $this->expectExceptionMessage(
            'Ptf\App\MyConfig::__get: Option "notSet" not configured');
        $config->notSet;
    }

    public function testSet()
    {
        $config = new MyConfig();

        $config->foobar     = 'baz';
        $config->testString = 'a string';
        $config->notSet     = 'set!';
        $this->assertSame('baz', $config->foobar);
        $this->assertSame('a string', $config->testString);
        $this->assertSame('set!', $config->notSet);
    }

    public function testIsSet()
    {
        $config = new MyConfig();

        $config->foobar = 'baz';
        $this->assertTrue(isset($config->testString));
        $this->assertTrue(isset($config->foobar));
        $this->assertTrue(isset($config->testArray));
        $this->assertFalse(isset($config->notSet));
        $this->assertFalse(isset($config->invalid));

        unset($config->foobar);
        unset($config->testString);
        unset($config->notSet);
        $this->assertFalse(isset($config->testString));
        $this->assertFalse(isset($config->foobar));
        $this->assertFalse(isset($config->notSet));
        $config->testString;
        $config->foobar;
        $config->notSet;
    }
}

class MyConfig extends Config
{
    public function __construct()
    {
        parent::__construct();

        $this->configData = array_merge($this->configData, [
          'testString'  => 'test123',
          'emptyString' => '',
          'notSet'      => null,
          'testArray'   => ['foo', 'bar']
        ]);
    }

    public function getConfigData()
    {
        return $this->configData;
    }
}

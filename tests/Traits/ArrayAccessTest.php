<?php

namespace Ptf\Traits;

class ArrayAccessTest extends \PHPUnit_Framework_TestCase
{
    public function testMagicGettersAndSetters()
    {
        $container = new TestContainer();
        $this->assertFalse(isset($container['foo']));
        $this->assertNull($container['foo']);
        $container['foo'] = 'bar';
        $this->assertTrue(isset($container['foo']));
        $this->assertSame('bar', $container['foo']);
        unset($container['foo']);
        $this->assertFalse(isset($container['foo']));
        $this->assertNull($container['foo']);
    }

}

class TestContainer implements \ArrayAccess
{
    use \Ptf\Traits\ArrayAccess;

    private $data = [];

    public function __get($name)
    {
        return array_key_exists($name, $this->data) ? $this->data[$name] : null;
    }

    public function __set($name, $value)
    {
        $this->data[$name] = $value;
    }

    public function __isset($name)
    {
        return isset($this->data[$name]);
    }

    public function __unset($name)
    {
        unset($this->data[$name]);
    }

}

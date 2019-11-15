<?php

namespace Ptf\Traits;

class SingletonTest extends \PHPUnit\Framework\TestCase
{
    public function testConstruct()
    {
        $singleton = MockSingleton::getInstance();
        $reflectionMethod = new \ReflectionMethod($singleton, '__construct');

        $this->assertTrue($reflectionMethod->isProtected());
        $this->assertFalse($reflectionMethod->isFinal());
    }

    public function testGetInstance()
    {
        $singleton = MockSingleton::getInstance();
        $reflectionMethod = new \ReflectionMethod($singleton, 'getInstance');

        $this->assertTrue($reflectionMethod->isFinal());

        $firstInstance = MockSingleton::getInstance();
        $firstInstance->foo = 'bar';
        $secondInstance = MockSingleton::getInstance();

        $this->assertSame('bar', $secondInstance->foo);
        $this->assertSame($firstInstance, $secondInstance);
    }

    public function testClone()
    {
        $singleton = MockSingleton::getInstance();
        $reflectionMethod = new \ReflectionMethod($singleton, '__clone');

        $this->assertTrue($reflectionMethod->isPrivate());
        $this->assertTrue($reflectionMethod->isFinal());
    }

    public function testWakeup()
    {
        $singleton = MockSingleton::getInstance();
        $reflectionMethod = new \ReflectionMethod($singleton, '__wakeup');

        $this->assertTrue($reflectionMethod->isPrivate());
        $this->assertTrue($reflectionMethod->isFinal());
    }
}

class MockSingleton
{
    use \Ptf\Traits\Singleton;
}

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
    }

    public function testWakeup()
    {
        $singleton = MockSingleton::getInstance();
        $this->expectException('\\Exception');
        $this->expectExceptionMessage('Ptf\Traits\MockSingleton::__wakeup: __wakeup of Singleton object is not allowed');

        $singleton->__wakeup();
    }
}

class MockSingleton
{
    use \Ptf\Traits\Singleton;
}

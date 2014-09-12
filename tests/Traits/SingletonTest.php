<?php

namespace PtfTest\Traits;

class SingletonTest extends \PHPUnit_Framework_TestCase
{
    private $traitName = '\Ptf\Traits\Singleton';

    public function testConstruct()
    {
        $singleton = $this->getObjectForTrait($this->traitName);
        $reflectionMethod = new \ReflectionMethod($singleton, '__construct');

        $this->assertTrue($reflectionMethod->isProtected());
        $this->assertFalse($reflectionMethod->isFinal());
    }

    public function testGetInstance()
    {
        $singleton = $this->getObjectForTrait($this->traitName);
        $reflectionMethod = new \ReflectionMethod($singleton, 'getInstance');

        $this->assertTrue($reflectionMethod->isFinal());

        $firstInstance = $singleton::getInstance();
        $firstInstance->foo = 'bar';
        $secondInstance = $singleton::getInstance();

        $this->assertSame($firstInstance, $secondInstance);
    }

    public function testClone()
    {
        $singleton = $this->getObjectForTrait($this->traitName);
        $reflectionMethod = new \ReflectionMethod($singleton, '__clone');

        $this->assertTrue($reflectionMethod->isPrivate());
        $this->assertTrue($reflectionMethod->isFinal());
    }

    public function testWakeup()
    {
        $singleton = $this->getObjectForTrait($this->traitName);
        $reflectionMethod = new \ReflectionMethod($singleton, '__wakeup');

        $this->assertTrue($reflectionMethod->isPrivate());
        $this->assertTrue($reflectionMethod->isFinal());
    }

}

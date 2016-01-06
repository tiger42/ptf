<?php

namespace  Ptf\Core\Cli;

use \Ptf\Core\Cli\Params;

class ParamsTest extends \PHPUnit_Framework_TestCase
{
    private $testParams1 = [
        'thescript.php',
        '-n',
        'test',
        'ABC',
        '--foo=bar',
        '-a',
        '-b',
        '-c',
        'this is a test'
    ];

    private $testParams2 = [
        'script.php',
        'task:run',
        'foo',
        'bar',
        'hello=world',
        '-t=test',
        'baz',
        '--param',
        'value',
        '-p',
        'a=b'
    ];

    public function testGetAll()
    {
        $oldArgv = $_SERVER['argv'];
        $oldArgc = $_SERVER['argc'];

        $_SERVER['argv'] = $this->testParams1;
        $_SERVER['argc'] = count($_SERVER['argv']);
        $params = new Params();
        $this->assertSame([
            '-n'    => 'test',
            'ABC'   => null,
            '--foo' => 'bar',
            '-a'    => null,
            '-b'    => null,
            '-c'    => 'this is a test'
        ], $params->getAll());

        $_SERVER['argv'] = $this->testParams2;
        $_SERVER['argc'] = count($_SERVER['argv']);
        $params = new Params();
        $this->assertSame([
            'task:run' => null,
            'foo'      => null,
            'bar'      => null,
            'hello'    => 'world',
            '-t'       => 'test',
            'baz'      => null,
            '--param'  => 'value',
            '-p'       => 'a=b'
        ], $params->getAll());

        $_SERVER['argv'] = $oldArgv;
        $_SERVER['argc'] = $oldArgc;
    }

    public function testGet()
    {
        $oldArgv = $_SERVER['argv'];
        $oldArgc = $_SERVER['argc'];

        $_SERVER['argv'] = $this->testParams1;
        $_SERVER['argc'] = count($_SERVER['argv']);
        $params = new Params();
        $this->assertSame('test', $params->get('-n'));
        $this->assertNull($params->get('ABC'));
        $this->assertSame('bar', $params->get('--foo'));
        $this->assertSame('this is a test', $params->get('-c'));
        $this->assertNull($params->get('invalid'));

        $_SERVER['argv'] = $oldArgv;
        $_SERVER['argc'] = $oldArgc;
    }

    public function testHas()
    {
        $oldArgv = $_SERVER['argv'];
        $oldArgc = $_SERVER['argc'];

        $_SERVER['argv'] = $this->testParams1;
        $_SERVER['argc'] = count($_SERVER['argv']);
        $params = new Params();
        $this->assertTrue($params->has('-n'));
        $this->assertTrue($params->has('ABC'));
        $this->assertTrue($params->has('--foo'));
        $this->assertTrue($params->has('-c'));
        $this->assertFalse($params->has('invalid'));

        $_SERVER['argv'] = $oldArgv;
        $_SERVER['argc'] = $oldArgc;
    }

}

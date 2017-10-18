<?php

namespace Ptf\App;

class ContextTest extends \PHPUnit\Framework\TestCase
{
    public function testConstructor()
    {
        $context = new MyContext();

        $this->assertInstanceOf('\\Ptf\\Core\\Cli\\Params', $context->getCliParams());
        $this->assertInstanceOf('\\Ptf\\Core\\Cli\\Output', $context->getCliOutput());
        $this->assertNull($context->getRequest());
        $this->assertNull($context->getResponse());

        $configs = $context->getConfigs();
        $this->assertCount(1, $configs);
        $this->assertInstanceOf('\\Ptf\\App\\Config\\General', $configs['General']);

        $this->assertSame([], $context->getRoutingTable());

        $this->assertSame($context->getView()->context, $context);
        $this->assertSame($context->getView()->request, $context->getRequest());
    }

    public function testConstructorHttp()
    {
        $context = new MyHttpContext();

        $this->assertInstanceOf('\\Ptf\\Core\\Http\\Request', $context->getRequest());
        $this->assertInstanceOf('\\Ptf\\Core\\Http\\Response', $context->getResponse());
        $this->assertNull($context->getCliParams());
        $this->assertNull($context->getCliOutput());

        $configs = $context->getConfigs();
        $this->assertCount(2, $configs);
        $this->assertInstanceOf('\\Ptf\\App\\Config\\General', $configs['General']);
        $this->assertInstanceOf('\\Ptf\\App\\Config\\ViewPlain', $configs['ViewPlain']);
    }

    public function testConstructorCallsMethods()
    {
        $className = '\Ptf\App\MyContext';
        $mock = $this->getMockBuilder($className)
            ->disableOriginalConstructor()
            ->setMethods(['initLoggers', 'init'])
            ->getMock();

        $mock->expects($this->once())
            ->method('init');
        $mock->expects($this->once())
            ->method('initLoggers');

        $reflection = new \ReflectionClass($className);
        $constructor = $reflection->getConstructor();
        $constructor->invoke($mock);
    }

    public function testConstructorException()
    {
        $this->expectException('\\Ptf\\Core\\Exception\\Config');
        $this->expectExceptionMessage(
            'Ptf\App\Config\ViewPlain::__get: Option \'template_dir\' not configured');
        new MyExceptionContext();
    }

    public function testInitLoggers()
    {
        $context = new MyContext();

        $loggers = $context->getLoggers();
        $this->assertCount(2, $loggers);
        $this->assertInstanceOf('\\Ptf\\Util\\Logger\\File', $loggers['system']);
        $this->assertInstanceOf('\\Ptf\\Util\\Logger\\File', $loggers['error']);
    }

    public function testGetDefaultControllerName()
    {
        $context = new MyContext();

        $this->assertSame('Base', $context->getDefaultControllerName());
    }

    public function testGetView()
    {
        $context = new MyContext();

        $this->assertInstanceOf('\\Ptf\\View\\Plain', $context->getView());
    }

    public function testGetLogger()
    {
        $context = new MyContext();

        $this->assertInstanceOf('\\Ptf\\Util\\Logger\\File', $context->getLogger());
        $this->assertInstanceOf('\\Ptf\\Util\\Logger\\File', $context->getLogger('error'));
        $this->assertInstanceOf('\\Ptf\\Util\\Logger\\DevNull', $context->getLogger('foobar'));
    }

    public function testSetController()
    {
        $context = new MyContext();

        $controller = new \Ptf\Controller\Base('foo', $context);
        $context->_setController($controller);
        $this->assertSame($controller, $context->getController());
    }

    public function testGetConfig()
    {
        $context = new MyContext();

        $this->assertInstanceOf('\\Ptf\\App\\Config\\General', $context->getConfig());
        $this->assertInstanceOf('\\PtfTest\\App\\Config\\ViewPlain', $context->getConfig('ViewPlain'));
    }

    public function testGetConfigException()
    {
        $context = new MyContext();

        $this->expectException('\\RuntimeException');
        $this->expectExceptionMessage(
            'Ptf\App\MyContext::getConfig: Configuration not found: foobar');
        $context->getConfig('foobar');
    }

    public function testGetBasePath()
    {
        $context = new MyContext();

        $this->assertSame($context->getBasePath(), $context->getBasePath(false));
        $this->assertSame($_SERVER['SCRIPT_FILENAME'], $context->getBasePath(true));
        $this->assertSame(0, strpos($context->getBasePath(true), $context->getBasePath()));
        $this->assertLessThan(strlen($context->getBasePath(true)), strlen($context->getBasePath()));
    }

    public function testGetBaseUrl()
    {
        $context = new MyContext();

        $this->assertSame($context->getBaseUrl(), $context->getBaseUrl(false));
        $this->assertSame($context->getBasePath(true), $context->getBaseUrl(true));
        $this->assertSame($context->getBasePath(), $context->getBaseUrl());
    }

    public function testGetBaseUrlHttp()
    {
        $context = new MyHttpContext();

        $oldHttpHost = $_SERVER['HTTP_HOST'] ?? null;
        $oldHttps    = $_SERVER['HTTPS'] ?? null;

        $_SERVER['HTTP_HOST'] = 'example.com';
        $this->assertSame('http://example.com' . $_SERVER['SCRIPT_FILENAME'], $context->getBaseUrl(true));
        $_SERVER['HTTPS'] = '1';
        $this->assertSame('https://example.com' . dirname($_SERVER['SCRIPT_FILENAME']), $context->getBaseUrl());

        $_SERVER['HTTP_HOST'] = $oldHttpHost;
        $_SERVER['HTTPS']     = $oldHttps;
    }
}

class MyContext extends Context
{
    public function __construct()
    {
        parent::__construct();
    }

    public function getAppNamespace(): string
    {
        return 'PtfTest';
    }

    public function getConfigs(): array
    {
        return $this->configs;
    }

    public function getLoggers(): array
    {
        return $this->loggers;
    }
}

class MyHttpContext extends MyContext
{
    public function isCli(): bool
    {
        return false;
    }
}

class MyExceptionContext extends MyHttpContext
{
    public function getConfig(string $configName = 'General'): \Ptf\App\Config
    {
        if ($configName == 'ViewPlain') {
            return new \Ptf\App\Config\ViewPlain();
        }
        return parent::getConfig($configName);
    }
}

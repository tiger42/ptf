<?php

namespace Ptf\App\Config;

class ViewSmartyTest extends \PHPUnit_Framework_TestCase
{
    public function testGetCompileDir()
    {
        $config = new ViewSmarty();

        $this->setExpectedException(
            '\\Ptf\\Core\\Exception\\Config',
            'Ptf\App\Config\ViewSmarty::__get: Option \'compile_dir\' not configured');
        $config->getCompileDir();
    }

    public function testGetCacheDir()
    {
        $config = new ViewSmarty();

        $this->assertSame('', $config->getCacheDir());
    }

    public function testGetCaching()
    {
        $config = new ViewSmarty();

        $this->assertSame('0', $config->getCaching());
    }

    public function testGetCacheLifetime()
    {
        $config = new ViewSmarty();

        $this->assertSame('0', $config->getCacheLifetime());
    }

    public function testGetPluginsDir()
    {
        $config = new ViewSmarty();

        $this->assertSame('', $config->getPluginsDir());
    }

    public function testGetCompressHtml()
    {
        $config = new ViewSmarty();

        $this->assertSame('1', $config->getCompressHtml());
    }

    public function testGetCompileCheck()
    {
        $config = new ViewSmarty();

        $this->assertSame('0', $config->getCompileCheck());
    }

    public function testGetForceCompile()
    {
        $config = new ViewSmarty();

        $this->assertSame('0', $config->getForceCompile());
    }
}

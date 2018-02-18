<?php

namespace Ptf\App\Config;

class ViewTest extends \PHPUnit\Framework\TestCase
{
    public function testGetTemplateDir()
    {
        $config = new View();

        $this->expectException('\\Ptf\\Core\\Exception\\Config');
        $this->expectExceptionMessage(
            'Ptf\App\Config\View::__get: Option "template_dir" not configured');
        $config->getTemplateDir();
    }

    public function testGetTemplate404()
    {
        $config = new View();

        $this->assertSame('', $config->getTemplate404());
    }
}

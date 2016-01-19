<?php

namespace Ptf\App\Config;

class ViewTest extends \PHPUnit_Framework_TestCase
{
    public function testGetTemplateDir()
    {
        $config = new View();

        $this->setExpectedException(
            '\\Ptf\\Core\\Exception\\Config',
            'Ptf\App\Config\View::__get: Option \'template_dir\' not configured');
        $config->getTemplateDir();
    }

    public function testGetTemplate404()
    {
        $config = new View();

        $this->assertSame('', $config->getTemplate404());
    }
}

<?php

namespace  Ptf\Core\Cli;

use \Ptf\Core\Cli\Output;

class OutputTest extends \PHPUnit\Framework\TestCase
{
    public function testSetContent()
    {
        $output = new Output();
        $this->assertNull($output->getContent());
        $this->assertFalse($output->hasContent());
        $output->setContent('this is the content');
        $this->assertSame('this is the content', $output->getContent());
        $this->assertTrue($output->hasContent());
    }

    public function testDisplay()
    {
        $output = new Output();
        ob_start();
        $output
            ->setContent('testcontent')
            ->display();
        $content = ob_get_clean();
        $this->assertSame('testcontent', $content);
    }
}

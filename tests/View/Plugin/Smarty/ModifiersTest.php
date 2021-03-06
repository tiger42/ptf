<?php

namespace Ptf\View\Plugin\Smarty;

class ModifiersTest extends \PHPUnit\Framework\TestCase
{
    public function testDblbr2p()
    {
        $input = "foo<br>bar<br><br>baz";
        $this->assertSame("foo<br>bar\n</p>\n<p>baz", Modifiers::dblbr2p($input));
        $input = "<br />foo<br/><br  >bar<br><br\t\t /><br />baz";
        $this->assertSame("<br />foo\n</p>\n<p>bar\n</p>\n<p><br />baz", Modifiers::dblbr2p($input));
        $input = "foo<br   ><br\t>bar<br />  <br><br >\n<br\t/>baz";
        $this->assertSame("foo\n</p>\n<p>bar\n</p>\n<p>\n</p>\n<p>baz", Modifiers::dblbr2p($input));
    }
}

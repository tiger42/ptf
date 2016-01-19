<?php

namespace Ptf\Util;

use Ptf\Util;

class FunctionsTest extends \PHPUnit_Framework_Testcase
{
    public function testNow()
    {
        $this->assertRegexp('/\d{4}-\d{2}-\d{2} \d{2}\:\d{2}\:\d{2}/', now());
    }

    public function testIsWholeNumber()
    {
        $this->assertTrue(is_whole_number(2));
        $this->assertTrue(is_whole_number('42'));
        $this->assertTrue(is_whole_number(5.0));
        $this->assertTrue(is_whole_number('5.0'));
        $this->assertFalse(is_whole_number(true));
        $this->assertFalse(is_whole_number('foo'));
        $this->assertFalse(is_whole_number(5.5));
        $this->assertFalse(is_whole_number('5.5'));
    }

    public function testEven()
    {
        $this->assertTrue(even(4));
        $this->assertTrue(even('100'));
        $this->assertTrue(even(0));
        $this->assertFalse(even(4.4));
        $this->assertFalse(even(5));
        $this->assertFalse(even('7'));
    }

    public function testOdd()
    {
        $this->assertFalse(odd(4));
        $this->assertFalse(odd('100'));
        $this->assertFalse(odd(0));
        $this->assertFalse(odd(4.4));
        $this->assertTrue(odd(5));
        $this->assertTrue(odd('7'));
    }

    public function testIsNumericArray()
    {
        $this->assertTrue(is_numeric_array(['foo', 'bar']));
        $this->assertTrue(is_numeric_array([42 => 'A', '100' => 'B']));
        $this->assertTrue(is_numeric_array([1, 2 => 3]));
        $this->assertTrue(is_numeric_array([]));
        $this->assertFalse(is_numeric_array([1, 'foo' => 'bar']));
    }

    public function testTruncate()
    {
        $str = 'Lorem ipsum dolor sit amet Consectetuer';
        $this->assertSame('Lorem ipsum', truncate($str, 11));
        $this->assertSame('Lorem ip...', truncate($str, 11, '...'));
        $this->assertSame('Lorem ipsum dolo-', truncate($str, 17, '-'));
        $this->assertSame('Lorem ipsum dolor sit amet Consectetuer', truncate($str, 40, '...'));
        $this->assertSame('Lorem ipsum dolor sit amet Consectetuer', truncate($str, 39, '...'));
        $this->assertSame('Lorem ipsum dolor sit amet Consecte...', truncate($str, 38, '...'));
        $this->assertSame('___', truncate($str, 3, '___'));
        $this->assertSame('_', truncate($str, 1, '___'));
        $this->assertSame('', truncate($str, 0, '___'));
    }

    public function testCamelize()
    {
        $str = 'this_is_a_camelized_string';
        $this->assertSame('ThisIsACamelizedString', camelize($str));
        $this->assertSame('ThisIsACamelizedString', camelize($str, false));
        $this->assertSame('thisIsACamelizedString', camelize($str, true));
        $this->assertSame('', camelize(''));
        $this->assertSame('ABC', camelize('ABC'));
        $this->assertSame('aBC', camelize('ABC', true));
        $this->assertSame('1Test', camelize('1_test'));
        $this->assertSame('1Test', camelize('1_test', true));
        $this->assertSame('Test', camelize('_test'));
        $this->assertSame('test', camelize('_test', true));
    }

    public function testPrettyPrintJson()
    {
        $json = '{"foo":{"bar":"baz","test":[1,2]}}';
        $compare = <<<EOT
{
    "foo": {
        "bar": "baz",
        "test": [
            1,
            2
        ]
    }
}
EOT;
        $this->assertSame($compare, prettyPrintJson($json));
    }
}

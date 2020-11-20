<?php

namespace Ptf\Util;

class FunctionsTest extends \PHPUnit\Framework\Testcase
{
    public function testNow()
    {
        $this->assertMatchesRegularExpression('/\d{4}-\d{2}-\d{2} \d{2}\:\d{2}\:\d{2}/', now());
    }

    public function testIsIntegerNumber()
    {
        $this->assertTrue(isIntegerNumber(2));
        $this->assertTrue(isIntegerNumber('42'));
        $this->assertTrue(isIntegerNumber(5.0));
        $this->assertTrue(isIntegerNumber('5.0'));
        $this->assertFalse(isIntegerNumber(true));
        $this->assertFalse(isIntegerNumber('foo'));
        $this->assertFalse(isIntegerNumber(5.5));
        $this->assertFalse(isIntegerNumber('5.5'));
        $this->assertTrue(isIntegerNumber(-6));
        $this->assertFalse(isIntegerNumber(-6.6));
    }

    public function testEven()
    {
        $this->assertTrue(even(4));
        $this->assertTrue(even('100'));
        $this->assertTrue(even(0));
        $this->assertFalse(even(4.4));
        $this->assertFalse(even(5));
        $this->assertFalse(even('7'));
        $this->assertTrue(even(-2.0));
        $this->assertFalse(even(-2.1));
        $this->assertFalse(even(3));
    }

    public function testOdd()
    {
        $this->assertFalse(odd(4));
        $this->assertFalse(odd('100'));
        $this->assertFalse(odd(0));
        $this->assertFalse(odd(4.4));
        $this->assertTrue(odd(5));
        $this->assertTrue(odd('7'));
        $this->assertFalse(odd(-2.0));
        $this->assertFalse(odd(-2.1));
        $this->assertTrue(odd(3));
    }

    public function testIsNumericArray()
    {
        $this->assertTrue(isNumericArray(['foo', 'bar']));
        $this->assertTrue(isNumericArray([42 => 'A', '100' => 'B']));
        $this->assertTrue(isNumericArray([1, 2 => 3]));
        $this->assertTrue(isNumericArray([]));
        $this->assertFalse(isNumericArray([1, 'foo' => 'bar']));
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
        $this->assertSame('fooBarBaz', camelize('foo<=>bar<=>baz', true, '<=>'));
        $this->assertSame('', camelize(''));
        $this->assertSame('ABC', camelize('ABC'));
        $this->assertSame('aBC', camelize('ABC', true));
        $this->assertSame('1Test', camelize('1_test'));
        $this->assertSame('1Test', camelize('1_test', true));
        $this->assertSame('Test', camelize('_test'));
        $this->assertSame('test', camelize('_test', true));
    }

    public function testUncamelize()
    {
        $str = 'ThisIsAnUnderscoredString';
        $this->assertSame('_this_is_an_underscored_string', uncamelize($str));
        $this->assertSame('-this-is-an-underscored-string', uncamelize($str, '-'));
        $this->assertSame('foo<=>bar<=>baz', uncamelize('fooBarBaz', '<=>'));
        $this->assertSame('', uncamelize(''));
        $this->assertSame('test', uncamelize('test'));
        $this->assertSame('_test', uncamelize('Test'));
        $this->assertSame('_a_b_c', uncamelize('ABC'));
        $this->assertSame('1_test', uncamelize('1Test'));
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

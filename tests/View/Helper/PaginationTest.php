<?php

namespace Ptf\View\Helper;

use \Ptf\View\Helper\Pagination;

class PaginationTest extends \PHPUnit_Framework_TestCase
{
    public function invalidParamsProvider()
    {
        return [
            ['a', 1, '1'],
            [1, 'a', 1],
            ['1', '1', 'a'],
            [null, 1, 1],
            [1, 1, 1, 'a']
        ];
    }

    public function paramsProvider()
    {
        return [
            [1, 2, 3, 5, [
                'pageCount' => 2,
                'currPage'  => 1,
                'prevPage'  => 1,
                'nextPage'  => 2,
                'pageList'  => [1, 2],
                'offset'    => 0
            ]],
            [0, 10, 100, 4, [
                'pageCount' => 10,
                'currPage'  => 1,
                'prevPage'  => 1,
                'nextPage'  => 2,
                'pageList'  => [1, 2, 3, 4],
                'offset'    => 0
            ]],
            [42, 43, 44, 10, [
                'pageCount' => 2,
                'currPage'  => 2,
                'prevPage'  => 1,
                'nextPage'  => 2,
                'pageList'  => [1, 2],
                'offset'    => 43
            ]],
            [-33, 99, 3, 2, [
                'pageCount' => 1,
                'currPage'  => 1,
                'prevPage'  => 1,
                'nextPage'  => 1,
                'pageList'  => [1],
                'offset'    => 0
            ]],
            [3, 5, 11, 3, [
                'pageCount' => 3,
                'currPage'  => 3,
                'prevPage'  => 2,
                'nextPage'  => 3,
                'pageList'  => [1, 2, 3],
                'offset'    => 10
            ]],
            [3, 5, 10, 3, [
                'pageCount' => 2,
                'currPage'  => 2,
                'prevPage'  => 1,
                'nextPage'  => 2,
                'pageList'  => [1, 2],
                'offset'    => 5
            ]],
            [5, 10, 88, 6, [
                'pageCount' => 9,
                'currPage'  => 5,
                'prevPage'  => 4,
                'nextPage'  => 6,
                'pageList'  => [3, 4, 5, 6, 7, 8],
                'offset'    => 40
            ]],
            [10, 4, 45, 3, [
                'pageCount' => 12,
                'currPage'  => 10,
                'prevPage'  => 9,
                'nextPage'  => 11,
                'pageList'  => [9, 10, 11],
                'offset'    => 36
            ]]
        ];
    }

    public function urlParamsProvider()
    {
        return [
            [[], ''],
            [[
                'url'  => 'http://example.com?page=1',
                'link' => 'link',
                'inactivelink' => 'x',
                'hideinactive' => 1,
                'delimiter'    => '|',
                'foo' => 'bar'
            ], '&amp;foo=bar'],
            [['a' => 1, 'b' => '2'], '&amp;a=1&amp;b=2'],
        ];
    }

    public function urlParamsWithSIDProvider()
    {
        return [
            [[], '&amp;SESS_ID=foobar'],
            [[
                'url'   => 'http://example.com/?p1=test',
                'hello' => 'world',
                'link'  => 'click',
                'test'  => '42',
                'hideinactive' => '0'
            ], '&amp;hello=world&amp;test=42&amp;SESS_ID=foobar'],
            [['a' => 1, 'b' => '2'], '&amp;a=1&amp;b=2&amp;SESS_ID=foobar'],
        ];
    }

    /**
     * @dataProvider        invalidParamsProvider
     * @expectedException   InvalidArgumentException
     * @expectedExceptionMessage Ptf\View\Helper\Pagination::__construct: Non-integer parameter given
     */
    public function testConstructorException($curPage, $itemsPerPage, $itemCount, $listCount = 5)
    {
        new Pagination($curPage, $itemsPerPage, $itemCount, $listCount);
    }

    /**
     * @dataProvider    paramsProvider
     */
    public function testGetPageCount($curPage, $itemsPerPage, $itemCount, $listCount, $expected)
    {
        $pagination = new Pagination($curPage, $itemsPerPage, $itemCount, $listCount);
        $this->assertSame($expected['pageCount'], $pagination->getPageCount());
    }

    /**
     * @dataProvider    paramsProvider
     */
    public function testGetCurrentPage($curPage, $itemsPerPage, $itemCount, $listCount, $expected)
    {
        $pagination = new Pagination($curPage, $itemsPerPage, $itemCount, $listCount, $expected);
        $this->assertSame($expected['currPage'], $pagination->getCurrentPage());
    }

    /**
     * @dataProvider    paramsProvider
     */
    public function testGetFirstPage($curPage, $itemsPerPage, $itemCount, $listCount)
    {
        $pagination = new Pagination($curPage, $itemsPerPage, $itemCount, $listCount);
        $this->assertSame(1, $pagination->getFirstPage());
    }

    /**
     * @dataProvider    paramsProvider
     */
    public function testGetLastPage($curPage, $itemsPerPage, $itemCount, $listCount)
    {
        $pagination = new Pagination($curPage, $itemsPerPage, $itemCount, $listCount);
        $this->assertSame($pagination->getPageCount(), $pagination->getLastPage());
    }

    /**
     * @dataProvider    paramsProvider
     */
    public function testGetPrevPage($curPage, $itemsPerPage, $itemCount, $listCount, $expected)
    {
        $pagination = new Pagination($curPage, $itemsPerPage, $itemCount, $listCount);
        $this->assertSame($expected['prevPage'], $pagination->getPrevPage());
    }

    /**
     * @dataProvider    paramsProvider
     */
    public function testGetNextPage($curPage, $itemsPerPage, $itemsCount, $listCount, $expected)
    {
        $pagination = new Pagination($curPage, $itemsPerPage, $itemsCount, $listCount);
        $this->assertSame($expected['nextPage'], $pagination->getNextPage());
    }

    /**
     * @dataProvider    paramsProvider
     */
    public function testGetPageList($curPage, $itemsPerPage, $itemsCount, $listCount, $expected)
    {
        $pagination = new Pagination($curPage, $itemsPerPage, $itemsCount, $listCount);
        $this->assertSame($expected['pageList'], $pagination->getPageList());
    }

    /**
     * @dataProvider    paramsProvider
     */
    public function testGetOffset($curPage, $itemsPerPage, $itemsCount, $listCount, $expected)
    {
        $pagination = new Pagination($curPage, $itemsPerPage, $itemsCount, $listCount);
        $this->assertSame($expected['offset'], $pagination->getOffset());
    }

    /**
     * @dataProvider    urlParamsProvider
     */
    public function testGenerateUrlParamsString($params, $expected)
    {
        $pagination = new Pagination(1, 1, 1);
        $this->assertSame($expected, $pagination->generateUrlParamsString($params));
    }

    /**
     * @dataProvider    urlParamsWithSIDProvider
     */
    public function testGenerateUrlParamsStringWithSID($params, $expected)
    {
        define('SID', 'SESS_ID=foobar');
        $pagination = new Pagination(1, 1, 1);
        $this->assertSame($expected, $pagination->generateUrlParamsString($params));
    }
}

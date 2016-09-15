<?php

namespace Ptf\View\Helper;

use Ptf\Util;

/**
 * Helper class for pagination* template plugins
 */
class Pagination
{
    /**
     * The number of the current page
     * @var integer
     */
    protected $curPage;

    /**
     * The count of list items per page
     * @var integer
     */
    protected $itemsPerPage;

    /**
     * The overall count of items
     * @var integer
     */
    protected $itemCount;

    /**
     * The count of pages to show in the page list
     * @var integer
     */
    protected $listCount;

    /**
     * The overall count of pages
     * @var integer
     */
    protected $pageCount;

    /**
     * Initialize the member variables
     *
     * @param   integer $curPage            Number of the current page
     * @param   integer $itemsPerPage       Count of items per page
     * @param   integer $itemCount          Overall count of items
     * @param   integer $listCount          Count of pages to show in the page list
     * @throws  \InvalidArgumentException   If any of the parameters is not integer
     */
    public function __construct($curPage, $itemsPerPage, $itemCount, $listCount = 5)
    {
        if (!Util\isIntegerNumber($curPage) || !Util\isIntegerNumber($itemsPerPage)
            || !Util\isIntegerNumber($itemCount) || !Util\isIntegerNumber($listCount)
        ) {
            throw new \InvalidArgumentException(get_class($this) . "::" . __FUNCTION__ . ": Non-integer parameter given");
        }
        $this->itemsPerPage = $itemsPerPage;
        $this->itemCount    = $itemCount;

        $pageCount = $this->getPageCount();

        if (($this->curPage = $curPage) > $pageCount) {
            $this->curPage = $pageCount;
        } elseif ($curPage < 1) {
            $this->curPage = 1;
        }

        if (($this->listCount = $listCount) > $pageCount) {
            $this->listCount = $pageCount;
        }
    }

    /**
     * Return the overall count of pages
     *
     * @return  integer                     The count of pages
     */
    public function getPageCount()
    {
        if ($this->pageCount === null) {
            $this->pageCount = floor($this->itemCount / $this->itemsPerPage);
            if ($this->itemCount % $this->itemsPerPage) {
                $this->pageCount++;
            }
        }
        return (int)$this->pageCount;
    }

    /**
     * Return the number of the current page
     *
     * @return  integer                     The number of the current page
     */
    public function getCurrentPage()
    {
        return $this->curPage;
    }

    /**
     * Return the number of the first page
     *
     * @return  integer                     The number of the first page
     */
    public function getFirstPage()
    {
        return 1;
    }

    /**
     * Return the number of the last page
     *
     * @return  integer                     The number of the last page
     */
    public function getLastPage()
    {
        return $this->getPageCount();
    }

    /**
     * Return the number of the previous page
     *
     * @return  integer                     The number of the previous page
     */
    public function getPrevPage()
    {
        $prevPage = $this->curPage - 1;
        if ($prevPage < 1) {
            $prevPage = 1;
        }
        return $prevPage;
    }

    /**
     * Return the number of the next page
     *
     * @return  integer                     The number of the next page
     */
    public function getNextPage()
    {
        $nextPage = $this->curPage + 1;
        if ($nextPage > ($pageCount = $this->getPageCount())) {
            $nextPage = $pageCount;
        }
        return $nextPage;
    }

    /**
     * Return a list of n page numbers (depending on $listCount)
     * surrounding the current page number
     *
     * @return  integer[]                   The list of page numbers
     */
    public function getPageList()
    {
        $start = $this->curPage - (int)($this->listCount / 2);
        if (Util\even($this->listCount)) {
            $start++;
        }
        if ($start < 1) {
            $start = 1;
        }
        $end = $start + $this->listCount - 1;
        $pageCount = $this->getPageCount();
        while ($end > $pageCount) {
            $start--;
            $end--;
        }

        $pages = [];
        for ($i = $start; $i <= $end; $i++) {
            $pages[] = $i;
        }
        return $pages;
    }

    /**
     * Get the offset for the first item of the current page
     *
     * @return  integer                     The current item offset
     */
    public function getOffset()
    {
        return $this->itemsPerPage * $this->curPage - $this->itemsPerPage;
    }

    /**
     * Extract parameters to append to the generated URL from the $params array
     * and return them as a string
     *
     * @param   array $params               Parameters for the pagination plugin
     * @return  string                      The generated parameters string
     */
    public function generateUrlParamsString(array $params)
    {
        $paginationParams = [
            'url'          => '',
            'link'         => '',
            'inactivelink' => '',
            'hideinactive' => '',
            'delimiter'    => ''
        ];

        $urlParams = array_diff_key($params, $paginationParams);
        $paramsStr = http_build_query($urlParams, '', '&amp;');

        if (strlen($paramsStr)) {
            $paramsStr = '&amp;' . $paramsStr;
        }
        if (defined('SID') && strlen(SID)) {
            $paramsStr .= '&amp;' . SID;
        }
        return $paramsStr;
    }
}

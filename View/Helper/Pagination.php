<?php

namespace Ptf\View\Helper;

use Ptf\Util;

/**
 * Helper class for pagination... template plugins.
 */
class Pagination
{
    /**
     * The number of the current page
     * @var int
     */
    protected $curPage;

    /**
     * The count of list items per page
     * @var int
     */
    protected $itemsPerPage;

    /**
     * The overall count of items
     * @var int
     */
    protected $itemCount;

    /**
     * The count of pages to show in the page list
     * @var int
     */
    protected $listCount;

    /**
     * The overall count of pages
     * @var int
     */
    protected $pageCount;

    /**
     * Initialize the member variables.
     *
     * @param int $curPage       Number of the current page
     * @param int $itemsPerPage  Count of items per page
     * @param int $itemCount     Overall count of items
     * @param int $listCount     Count of pages to show in the page list
     */
    public function __construct(int $curPage, int $itemsPerPage, int $itemCount, int $listCount = 5)
    {
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
     * Return the overall count of pages.
     *
     * @return int  The count of pages
     */
    public function getPageCount(): int
    {
        if ($this->pageCount === null) {
            $this->pageCount = floor($this->itemCount / $this->itemsPerPage);
            if ($this->itemCount % $this->itemsPerPage) {
                $this->pageCount++;
            }
        }
        return $this->pageCount;
    }

    /**
     * Return the number of the current page.
     *
     * @return int  The number of the current page
     */
    public function getCurrentPage(): int
    {
        return $this->curPage;
    }

    /**
     * Return the number of the first page.
     *
     * @return int  The number of the first page
     */
    public function getFirstPage(): int
    {
        return 1;
    }

    /**
     * Return the number of the last page.
     *
     * @return int  The number of the last page
     */
    public function getLastPage(): int
    {
        return $this->getPageCount();
    }

    /**
     * Return the number of the previous page.
     *
     * @return int  The number of the previous page
     */
    public function getPrevPage(): int
    {
        $prevPage = $this->curPage - 1;
        if ($prevPage < 1) {
            $prevPage = 1;
        }
        return $prevPage;
    }

    /**
     * Return the number of the next page.
     *
     * @return int  The number of the next page
     */
    public function getNextPage(): int
    {
        $nextPage = $this->curPage + 1;
        if ($nextPage > ($pageCount = $this->getPageCount())) {
            $nextPage = $pageCount;
        }
        return $nextPage;
    }

    /**
     * Return a list of n page numbers (depending on $listCount)
     * surrounding the current page number.
     *
     * @return int[]  The list of page numbers
     */
    public function getPageList(): array
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
     * Get the offset for the first item of the current page.
     *
     * @return int  The current item offset
     */
    public function getOffset(): int
    {
        return $this->itemsPerPage * $this->curPage - $this->itemsPerPage;
    }

    /**
     * Extract parameters to append to the generated URL from the $params array
     * and return them as a string.
     *
     * @param array $params  Parameters for the pagination plugin
     *
     * @return string  The generated parameters string
     */
    public function generateUrlParamsString(array $params): string
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

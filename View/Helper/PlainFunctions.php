<?php

namespace Ptf\View\Helper;

/**
 * Plain view template function plugins
 */
class PlainFunctions
{
    /**
     * Register all Plain view function plugins of this class
     *
     * @param   \Ptf\View\Plain $view       The Plain view object
     */
    public static function register(\Ptf\View\Plain $view)
    {
        // Pagination plugins
        $view->registerPlugin('pagination_first', [__CLASS__, 'paginationFirst']);
        $view->registerPlugin('pagination_prev', [__CLASS__, 'paginationPrev']);
        $view->registerPlugin('pagination_next', [__CLASS__, 'paginationNext']);
        $view->registerPlugin('pagination_last', [__CLASS__, 'paginationLast']);
        $view->registerPlugin('pagination_list', [__CLASS__, 'paginationList']);
        $view->registerPlugin('pagination_count', [__CLASS__, 'paginationCount']);

        $view->registerPlugin('dblbr2p', [__CLASS__, 'dblbr2p']);
        $view->registerPlugin('sid', [__CLASS__, 'sid']);
    }

    /**
     * Generate a link to the first page of a listing with pagination
     * or return its page number
     *
     * <pre>
     * Available parameters:
     * url           The URL of the page (without any parameters!)
     *               If not set, the plugin will return the page number only
     * link          The link text or image
     *               If not set, the default '|<' will be used
     * inactivelink  The link text or image if the current page is the first page
     *               If not set, the default '|<' will be used
     * hideinactive  If the current page is the first page:
     *               Hide the link text or image?
     * Any additional parameters will be appended to the URL
     * </pre>
     *
     * @param   array $params               Parameters for the plugin
     * @param   \Ptf\View\Plain $view       The view object
     * @return  string                      The generated pagination link
     */
    public static function paginationFirst(array $params, \Ptf\View\Plain $view)
    {
        $pagination = $view['pagination'];
        if (!($pagination instanceof \Ptf\View\Helper\Pagination)) {
            trigger_error(__FUNCTION__ . "(): No Pagination object set", E_USER_ERROR);
        }

        $default = '|&lt;';
        if (isset($params['url'])) {
            if ($pagination->getFirstPage() == $pagination->getCurrentPage()) {
                if (isset($params['hideinactive']) && $params['hideinactive']) {
                    return '';
                }
                $link = isset($params['inactivelink']) ? $params['inactivelink'] : $default;

                return $link;
            } else {
                $link = isset($params['link']) ? $params['link'] : $default;
                $urlParams = $pagination->generateUrlParamsString($params);

                return '<a href="' . $params['url'] .'?page=' . $pagination->getFirstPage() . $urlParams . '">' . $link . '</a>';
            }
        }
        return $pagination->getFirstPage();
    }

    /**
     * Generate a link to the previous page of a listing with pagination
     * or return its page number
     *
     * <pre>
     * Available parameters:
     * url           The URL of the page (without any parameters!)
     *               If not set, the plugin will return the page number only
     * link          The link text or image
     *               If not set, the default '<' will be used
     * inactivelink  The link text or image if the current page is the first page
     *               If not set, the default '<' will be used
     * hideinactive  If the current page is the first page:
     *               Hide the link text or image?
     * Any additional parameters will be appended to the URL
     * </pre>
     *
     * @param   array $params               Parameters for the plugin
     * @param   \Ptf\View\Plain $view       The view object
     * @return  string                      The generated pagination link
     */
    public static function paginationPrev(array $params, \Ptf\View\Plain $view)
    {
        $pagination = $view['pagination'];
        if (!($pagination instanceof \Ptf\View\Helper\Pagination)) {
            trigger_error(__FUNCTION__ . "(): No Pagination object set", E_USER_ERROR);
        }

        $default = '&lt;';
        if (isset($params['url'])) {
            if ($pagination->getPrevPage() == $pagination->getCurrentPage()) {
                if (isset($params['hideinactive']) && $params['hideinactive']) {
                    return '';
                }
                $link = isset($params['inactivelink']) ? $params['inactivelink'] : $default;

                return $link;
            } else {
                $link = isset($params['link']) ? $params['link'] : $default;
                $urlParams = $pagination->generateUrlParamsString($params);

                return '<a href="' . $params['url'] .'?page=' . $pagination->getPrevPage() . $urlParams . '">' . $link . '</a>';
            }
        }
        return $pagination->getPrevPage();
    }

    /**
     * Generate a link to the next page of a listing with pagination
     * or return its page number
     *
     * <pre>
     * Available parameters:
     * url           The URL of the page (without any parameters!)
     *               If not set, the plugin will return the page number only
     * link          The link text or image
     *               If not set, the default '>' will be used
     * inactivelink  The link text or image if the current page is the last page
     *               If not set, the default '>' will be used
     * hideinactive  If the current page is the last page:
     *               Hide the link text or image?
     * Any additional parameters will be appended to the URL
     * </pre>
     *
     * @param   array $params               Parameters for the plugin
     * @param   \Ptf\View\Plain $view       The view object
     * @return  string                      The generated pagination link
     */
    public static function paginationNext(array $params, \Ptf\View\Plain $view)
    {
        $pagination = $view['pagination'];
        if (!($pagination instanceof \Ptf\View\Helper\Pagination)) {
            trigger_error(__FUNCTION__ . "(): No Pagination object set", E_USER_ERROR);
        }

        $default = '&gt;';
        if (isset($params['url'])) {
            if ($pagination->getNextPage() == $pagination->getCurrentPage()) {
                if (isset($params['hideinactive']) && $params['hideinactive']) {
                    return '';
                }
                $link = isset($params['inactivelink']) ? $params['inactivelink'] : $default;

                return $link;
            } else {
                $link = isset($params['link']) ? $params['link'] : $default;
                $urlParams = $pagination->generateUrlParamsString($params);

                return '<a href="' . $params['url'] .'?page=' . $pagination->getNextPage() . $urlParams . '">' . $link . '</a>';
            }
        }
        return $pagination->getNextPage();
    }

    /**
     * Generate a link to the last page of a listing with pagination
     * or return its page number
     *
     * <pre>
     * Available parameters:
     * url           The URL of the page (without any parameters!)
     *               If not set, the plugin will return the page number only
     * link          The link text or image
     *               If not set, the default '>|' will be used
     * inactivelink  The link text or image if the current page is the last page
     *               If not set, the default '>|' will be used
     * hideinactive  If the current page is the last page:
     *               Hide the link text or image?
     * Any additional parameters will be appended to the URL
     * </pre>
     *
     * @param   array $params               Parameters for the plugin
     * @param   \Ptf\View\Plain $view       The view object
     * @return  string                      The generated pagination link
     */
    public static function paginationLast(array $params, \Ptf\View\Plain $view)
    {
        $pagination = $view['pagination'];
        if (!($pagination instanceof \Ptf\View\Helper\Pagination)) {
            trigger_error(__FUNCTION__ . "(): No Pagination object set", E_USER_ERROR);
        }

        $default = '&gt;|';
        if (isset($params['url'])) {
            if ($pagination->getLastPage() == $pagination->getCurrentPage()) {
                if (isset($params['hideinactive']) && $params['hideinactive']) {
                    return '';
                }
                $link = isset($params['inactivelink']) ? $params['inactivelink'] : $default;

                return $link;
            } else {
                $link = isset($params['link']) ? $params['link'] : $default;
                $urlParams = $pagination->generateUrlParamsString($params);

                return '<a href="' . $params['url'] .'?page=' . $pagination->getLastPage() . $urlParams . '">' . $link . '</a>';
            }
        }
        return $pagination->getLastPage();
    }

    /**
     * Generate a list of page numbers (with links) for a listing with pagination
     *
     * <pre>
     * Available parameters:
     * url        The URL of the page (without any parameters!)
     * delimiter  The delimiter between two page numbers
     *            If not set, the default ' ' will be used
     * Any additional parameters will be appended to the URL
     * </pre>
     *
     * @param   array $params               Parameters for the plugin
     * @param   \Ptf\View\Plain $view       The view object
     * @return  string                      The generated page list
     */
    public static function paginationList(array $params, \Ptf\View\Plain $view)
    {
        $pagination = $view['pagination'];
        if (!($pagination instanceof \Ptf\View\Helper\Pagination)) {
            trigger_error(__FUNCTION__ . "(): No Pagination object set", E_USER_ERROR);
        }

        if (!isset($params['url'])) {
            return '';
        }

        $str = '';
        $delimiter = isset($params['delimiter']) ? $params['delimiter'] : ' ';
        $urlParams = $pagination->generateUrlParamsString($params);
        $pageList  = $pagination->getPageList();
        $current   = $pagination->getCurrentPage();
        foreach ($pageList as $page) {
            $str .= ($page == $current) ? $page : '<a href="' . $params['url'] . '?page=' . $page . $urlParams . '">' . $page . '</a>';
            $str .= $delimiter;
        }
        $str = substr($str, 0, -strlen($delimiter));

        return $str;
    }

    /**
     * Display the overall number of pages for a pagination
     *
     * @param   \Ptf\View\Plain $view       The view object
     * @return  integer                     The page count
     */
    public static function paginationCount(\Ptf\View\Plain $view)
    {
        $pagination = $view['pagination'];
        if (!($pagination instanceof \Ptf\View\Helper\Pagination)) {
            trigger_error(__FUNCTION__ . "(): No Pagination object set", E_USER_ERROR);
        }

        return $pagination->getPageCount();
    }

    /**
     * Replace two consecutive "<br />" with "</p><p>"
     *
     * @param   string $string              The string to be modified
     * @param   \Ptf\View\Plain             The view object
     * @return  string                      The modified string
     */
    public static function dblbr2p($string, \Ptf\View\Plain $view)
    {
        return preg_replace('/<br *\/?>\s*<br *\/?>/m', "\n</p>\n<p>", $string);
    }

    /**
     * Insert the value of the SID constant
     *
     * @param   \Ptf\View\Plain $view       The view object
     * @return  string                      The SID constant
     */
    public static function sid(\Ptf\View\Plain $view)
    {
        return defined('SID') ? SID : '';
    }
}

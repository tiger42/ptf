<?php

namespace Ptf\View\Plugin\Smarty;

use \Ptf\View\Helper\Pagination;

/**
 * Smarty template function plugins for pagination
 */
class FunctionsPagination
{
    /**
     * Register all Smarty function plugins of this class
     *
     * @param   \Ptf\View\Smarty $view      The Smarty view object
     */
    public static function register(\Ptf\View\Smarty $view)
    {
        $view->registerFunctionPlugin('pagination_first', [__CLASS__, 'paginationFirst']);
        $view->registerFunctionPlugin('pagination_prev', [__CLASS__, 'paginationPrev']);
        $view->registerFunctionPlugin('pagination_next', [__CLASS__, 'paginationNext']);
        $view->registerFunctionPlugin('pagination_last', [__CLASS__, 'paginationLast']);
        $view->registerFunctionPlugin('pagination_list', [__CLASS__, 'paginationList']);
        $view->registerFunctionPlugin('pagination_count', [__CLASS__, 'paginationCount']);
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
     * @param   array $params                        Parameters for the plugin
     * @param   \Smarty_Internal_Template $template  The Smarty template object
     * @return  string                               The generated pagination link
     */
    public static function paginationFirst(array $params, \Smarty_Internal_Template $template)
    {
        $pagination = $template->getTemplateVars('pagination');
        if (!($pagination instanceof Pagination)) {
            trigger_error(__FUNCTION__ . "(): No Pagination object set", E_USER_ERROR);
        }

        $default = '|&lt;';
        if (isset($params['url'])) {
            if ($pagination->getFirstPage() == $pagination->getCurrentPage()) {
                if (isset($params['hideinactive']) && $params['hideinactive']) {
                    return '';
                }
                $link = $params['inactivelink'] ?? $default;

                return $link;
            } else {
                $link = $params['link'] ?? $default;
                $urlParams = $pagination->generateUrlParamsString($params);

                return '<a href="' . $params['url'] . '?page=' . $pagination->getFirstPage() . $urlParams . '">' . $link . '</a>';
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
     * @param   array $params                        Parameters for the plugin
     * @param   \Smarty_Internal_Template $template  The Smarty template object
     * @return  string                               The generated pagination link
     */
    public static function paginationPrev(array $params, \Smarty_Internal_Template $template)
    {
        $pagination = $template->getTemplateVars('pagination');
        if (!($pagination instanceof Pagination)) {
            trigger_error(__FUNCTION__ . "(): No Pagination object set", E_USER_ERROR);
        }

        $default = '&lt;';
        if (isset($params['url'])) {
            if ($pagination->getPrevPage() == $pagination->getCurrentPage()) {
                if (isset($params['hideinactive']) && $params['hideinactive']) {
                    return '';
                }
                $link = $params['inactivelink'] ?? $default;

                return $link;
            } else {
                $link = $params['link'] ?? $default;
                $urlParams = $pagination->generateUrlParamsString($params);

                return '<a href="' . $params['url'] . '?page=' . $pagination->getPrevPage() . $urlParams . '">' . $link . '</a>';
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
     * @param   array $params                        Parameters for the plugin
     * @param   \Smarty_Internal_Template $template  The Smarty template object
     * @return  string                               The generated pagination link
     */
    public static function paginationNext(array $params, \Smarty_Internal_Template $template)
    {
        $pagination = $template->getTemplateVars('pagination');
        if (!($pagination instanceof Pagination)) {
            trigger_error(__FUNCTION__ . "(): No Pagination object set", E_USER_ERROR);
        }

        $default = '&gt;';
        if (isset($params['url'])) {
            if ($pagination->getNextPage() == $pagination->getCurrentPage()) {
                if (isset($params['hideinactive']) && $params['hideinactive']) {
                    return '';
                }
                $link = $params['inactivelink'] ?? $default;

                return $link;
            } else {
                $link = $params['link'] ?? $default;
                $urlParams = $pagination->generateUrlParamsString($params);

                return '<a href="' . $params['url'] . '?page=' . $pagination->getNextPage() . $urlParams . '">' . $link . '</a>';
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
     * @param   array $params                        Parameters for the plugin
     * @param   \Smarty_Internal_Template $template  The Smarty template object
     * @return  string                               The generated pagination link
     */
    public static function paginationLast(array $params, \Smarty_Internal_Template $template)
    {
        $pagination = $template->getTemplateVars('pagination');
        if (!($pagination instanceof Pagination)) {
            trigger_error(__FUNCTION__ . "(): No Pagination object set", E_USER_ERROR);
        }

        $default = '&gt;|';
        if (isset($params['url'])) {
            if ($pagination->getLastPage() == $pagination->getCurrentPage()) {
                if (isset($params['hideinactive']) && $params['hideinactive']) {
                    return '';
                }
                $link = $params['inactivelink'] ?? $default;

                return $link;
            } else {
                $link = $params['link'] ?? $default;
                $urlParams = $pagination->generateUrlParamsString($params);

                return '<a href="' . $params['url'] . '?page=' . $pagination->getLastPage() . $urlParams . '">' . $link . '</a>';
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
     * @param   array $params                        Parameters for the plugin
     * @param   \Smarty_Internal_Template $template  The Smarty template object
     * @return  string                               The generated page list
     */
    public static function paginationList(array $params, \Smarty_Internal_Template $template)
    {
        $pagination = $template->getTemplateVars('pagination');
        if (!($pagination instanceof Pagination)) {
            trigger_error(__FUNCTION__ . "(): No Pagination object set", E_USER_ERROR);
        }

        if (!isset($params['url'])) {
            return '';
        }

        $str = '';
        $delimiter = $params['delimiter'] ?? ' ';
        $urlParams = $pagination->generateUrlParamsString($params);
        $pageList  = $pagination->getPageList();
        $current   = $pagination->getCurrentPage();
        foreach ($pageList as $page) {
            $str .= ($page == $current)
                ? $page
                : '<a href="' . $params['url'] . '?page=' . $page . $urlParams . '">' . $page . '</a>';
            $str .= $delimiter;
        }
        $str = substr($str, 0, -strlen($delimiter));

        return $str;
    }

    /**
     * Display the overall number of pages for a pagination
     *
     * @param   array $params                        Parameters for the plugin
     * @param   \Smarty_Internal_Template $template  The Smarty template object
     * @return  int                              The page count
     */
    public static function paginationCount(array $params, \Smarty_Internal_Template $template)
    {
        $pagination = $template->getTemplateVars('pagination');
        if (!($pagination instanceof Pagination)) {
            trigger_error(__FUNCTION__ . "(): No Pagination object set", E_USER_ERROR);
        }

        return $pagination->getPageCount();
    }
}

<?php

namespace Ptf\View\Plugin\Plain;

/**
 * Plain view template function plugins
 */
class Functions
{
    /**
     * Register all Plain view function plugins of this class
     *
     * @param   \Ptf\View\Plain $view       The Plain view object
     */
    public static function register(\Ptf\View\Plain $view)
    {
        $view->registerFunctionPlugin('dblbr2p', [__CLASS__, 'dblbr2p']);
        $view->registerFunctionPlugin('sid', [__CLASS__, 'sid']);

        Functions_Pagination::register($view);
    }

    /**
     * Replace two consecutive "<br />" with "</p><p>"
     *
     * @param   string $string              The string to be modified
     * @return  string                      The modified string
     */
    public static function dblbr2p($string)
    {
        return preg_replace('/<br *\/?>\s*<br *\/?>/m', "\n</p>\n<p>", $string);
    }

    /**
     * Insert the value of the SID constant
     *
     * @return  string                      The SID constant
     */
    public static function sid()
    {
        return defined('SID') ? SID : '';
    }
}

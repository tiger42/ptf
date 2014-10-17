<?php

namespace Ptf\View\Helper;

/**
 * Smarty template modifier plugins
 */
class SmartyModifiers
{
    /**
     * Register all Smarty modifier plugins of this class
     *
     * @param   \Smarty $smarty             The Smarty object
     */
    public static function register(\Smarty $smarty)
    {
        $smarty->registerPlugin('modifier', 'dblbr2p', array(__CLASS__, 'dblbr2p'));
    }

    /**
     * Replace two consecutive "<br />" with "</p><p>"
     *
     * @param   string $string              The string to be modified
     * @return  string                      The modified string
     */
    public static function dblbr2p($string)
    {
        return preg_replace('/<br[\s]*\/?>\s*<br[\s]*\/?>/m', "\n</p>\n<p>", $string);
    }

}

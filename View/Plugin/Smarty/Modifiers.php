<?php

namespace Ptf\View\Plugin\Smarty;

/**
 * Smarty template modifier plugins
 */
class Modifiers
{
    /**
     * Register all Smarty modifier plugins of this class
     *
     * @param   \Ptf\View\Smarty $view      The Smarty view object
     */
    public static function register(\Ptf\View\Smarty $view)
    {
        $view->registerModifierPlugin('dblbr2p', [__CLASS__, 'dblbr2p']);
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

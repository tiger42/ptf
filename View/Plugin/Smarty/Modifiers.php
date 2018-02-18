<?php

namespace Ptf\View\Plugin\Smarty;

use Ptf\View\Smarty as View;

/**
 * Smarty template modifier plugins.
 */
class Modifiers
{
    /**
     * Register all Smarty modifier plugins of this class.
     *
     * @param View $view  The Smarty view object
     */
    public static function register(View $view): void
    {
        $view->registerModifierPlugin('dblbr2p', [__CLASS__, 'dblbr2p']);
    }

    /**
     * Replace two consecutive "<br />" with "</p><p>".
     *
     * @param  string $string  The string to be modified
     *
     * @return string  The modified string
     */
    public static function dblbr2p(string $string): string
    {
        return preg_replace('/<br[\s]*\/?>\s*<br[\s]*\/?>/m', "\n</p>\n<p>", $string);
    }
}

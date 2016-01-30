<?php

namespace Ptf\View\Plugin\Smarty;

/**
 * Smarty template function plugins
 */
class Functions
{
    /**
     * Register all Smarty function plugins of this class
     *
     * @param   \Ptf\View\Smarty $view      The Smarty view object
     */
    public static function register(\Ptf\View\Smarty $view)
    {
        $view->registerFunctionPlugin('sid', [__CLASS__, 'sid']);

        Functions_Pagination::register($view);
    }

    /**
     * Get the value of the SID constant
     *
     * @return  string                      The SID constant
     */
    public static function sid()
    {
        return defined('SID') ? SID : '';
    }
}

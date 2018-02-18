<?php

namespace Ptf\View\Plugin\Smarty;

use Ptf\View\Smarty as View;
use Smarty_Internal_Template as Smarty;

/**
 * Smarty template block function plugins.
 */
class Blocks
{
    /**
     * Register all Smarty block function plugins of this class.
     *
     * @param View $view  The Smarty view object
     */
    public static function register(View $view): void
    {
        $view->registerBlockPlugin('language', [__CLASS__, 'language']);

        BlocksDB::register($view);
    }

    /**
     * Display content depending on the given language code.
     *
     * <pre>
     * Available parameters:
     *   code  The language code (e.g. 'en', 'de', ...)
     * </pre>
     *
     * @param array  $params    Parameters for the plugin
     * @param string $content   Content of the block tags
     * @param Smarty $template  The Smarty template object
     * @param bool   $repeat    Repeat the plugin?
     *
     * @return string  The language dependent string
     */
    public static function language(array $params, string $content = null, Smarty $template, bool &$repeat): string
    {
        if (!isset($params['code']) || !strlen($params['code'])) {
            trigger_error(__FUNCTION__ . '(): No language code given', E_USER_ERROR);
        }

        // Skip the opening block tag
        if ($content === null) {
            return '';
        }

        $language = $params['code'];

        $tplLanguage = $template->getTemplateVars('tplLanguage');
        if (!strlen($tplLanguage)) {
            $tplLanguage = 'en';
        }
        if ($language != $tplLanguage) {
            return '';
        }

        $repeat = false;

        return $content;
    }
}

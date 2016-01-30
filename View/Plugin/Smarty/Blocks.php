<?php

namespace Ptf\View\Plugin\Smarty;

/**
 * Smarty template block function plugins
 */
class Blocks
{
    /**
     * Register all Smarty block function plugins of this class
     *
     * @param   \Ptf\View\Smarty $view      The Smarty view object
     */
    public static function register(\Ptf\View\Smarty $view)
    {
        $view->registerBlockPlugin('language', [__CLASS__, 'language']);

        Blocks_DB::register($view);
    }

    /**
     * Display content depending on the given language code
     *
     * <pre>
     * Available parameters:
     * code  The language code (e.g. 'en', 'de', ...)
     * </pre>
     *
     * @param   array $params                        Parameters for the plugin
     * @param   string $content                      Content of the block tags
     * @param   \Smarty_Internal_Template $template  The Smarty template object
     * @param   boolean $repeat                      Repeat the plugin?
     * @return  string                               The language dependent string
     */
    public static function language(array $params, $content, \Smarty_Internal_Template $template, &$repeat)
    {
        if (!isset($params['code']) || !strlen($params['code'])) {
            trigger_error(__FUNCTION__ . "(): No language code given", E_USER_ERROR);
        }

        // Skip the opening block tag
        if ($content === null) {
            return;
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

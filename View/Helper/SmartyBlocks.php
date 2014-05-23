<?php

namespace Ptf\View\Helper;

/**
 * Smarty template block function plugins
 */
class SmartyBlocks
{
    /**
     * Register all Smarty block function plugins of this class
     *
     * @param   \Smarty $smarty             The Smarty object
     */
    public static function register(\Smarty $smarty)
    {
        $smarty->registerPlugin('block', 'fetch_db', array(__CLASS__, 'fetchDB'));
        $smarty->registerPlugin('block', 'fetch_dbtable', array(__CLASS__, 'fetchDBTable'));

        $smarty->registerPlugin('block', 'language', array(__CLASS__, 'language'));
    }

    /**
     * Fetch rows of a DB object
     *
     * <pre>
     * Available parameters:
     * db           The DB object
     *
     * Assigned template variables:
     * fetchcount   The current number of fetches
     * row          The currently fetched row
     * </pre>
     *
     * @param   array $params                       Parameters for the plugin
     * @param   string $content                     Content of the block tags
     * @param   \Smarty_Internal_Template $template The Smarty template object
     * @param   boolean $repeat                     Repeat the plugin?
     * @param   integer $fetchCount                 The number of performed fetches
     * @return  string                              The modified string
     */
    public static function fetchDB(array $params, $content, \Smarty_Internal_Template $template, &$repeat, &$fetchCount = 0)
    {
        if (!isset($params['db']) || !($params['db'] instanceof \Ptf\Model\DB)) {
            trigger_error(__FUNCTION__ . "(): No DB object set", E_USER_ERROR);
        }

        $db = $params['db'];

        // Skip the opening block tag
        if ($content !== null) {
            $row = $db->fetch();
            $fetchCount = 1;

            $template->assign('row', $row);
            $template->assign('fetchcount', $fetchCount);
            return;
        }
        $row = $db->fetch();
        $repeat = $row !== false;
        $fetchCount++;

        $template->assign('row', $row);
        $template->assign('fetchcount', $fetchCount);

        return $content;
    }

    /**
     * Fetch rows of a DB\Table object
     *
     * <pre>
     * Available parameters:
     * dbtable      The DB\Table object
     * offset       Offset of the first row to fetch (default: 0)
     * count        Number of rows to fetch (default: all)
     *
     * Assigned template variables:
     * fetchcount   The current number of fetches
     * </pre>
     *
     * @param   array $params                       Parameters for the plugin
     * @param   string $content                     Content of the block tags
     * @param   \Smarty_Internal_Template $template The Smarty template object
     * @param   boolean &$repeat                    Repeat the plugin?
     * @param   integer $fetchCount                 The number of performed fetches
     * @return  string                              The modified string
     */
    public static function fetchDBTable(array $params, $content, \Smarty_Internal_Template $template, &$repeat, &$fetchCount = 0)
    {
        if (!isset($params['dbtable']) || !($params['dbtable'] instanceof \Ptf\Model\DB\Table)) {
            trigger_error(__FUNCTION__ . "(): No DB table object set", E_USER_ERROR);
        }

        $dbtable = $params['dbtable'];
        $offset  = isset($params['offset']) ? $params['offset'] : 0;
        $count   = isset($params['count']) ? $params['count'] : null;

        // Skip the opening block tag
        if ($content === null) {
            $dbtable->fetch($offset, $count);
            $fetchCount = 1;
            $template->assign('fetchcount', $fetchCount);
            return;
        }
        $repeat = $dbtable->fetch();
        $fetchCount++;
        $template->assign('fetchcount', $fetchCount);

        return $content;
    }

    /**
     * Display content depending on the given language code
     *
     * <pre>
     * Available parameters:
     * code     The language code (e.g. 'en', 'de', ...)
     * </pre>
     *
     * @param   array $params                       Parameters for the plugin
     * @param   string $content                     Content of the block tags
     * @param   \Smarty_Internal_Template $template The Smarty template object
     * @param   boolean $repeat                     Repeat the plugin?
     * @return  string                              The language dependent string
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

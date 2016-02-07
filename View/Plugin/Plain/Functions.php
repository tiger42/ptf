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
        $view->registerFunctionPlugin('exec', [__CLASS__, 'exec']);

        FunctionsPagination::register($view);
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

    /**
     * Insert the value of the SID constant
     *
     * @return  string                      The SID constant
     */
    public static function sid()
    {
        return defined('SID') ? SID : '';
    }

    /**
     * Execute the given controller action
     *
     * <pre>
     * Available parameters:
     * controller  The controller containing the action to execute
     * action      The action to execute
     * Any additional parameters will be set as "virtual" GET/REQUEST parameters
     * </pre>
     *
     * @param   array $params               Parameters for the plugin
     * @param   \Ptf\View\Plain $view       The view object
     * @return  string                      The content of the Response object, if set
     */
    public static function exec(array $params, \Ptf\View\Plain $view)
    {
        /** @var $context \Ptf\App\Context */
        $context  = $view['context'];
        $response = $context->getResponse();

        $controllerName = isset($params['controller']) ? $params['controller'] : '';
        $actionName     = isset($params['action']) ? $params['action'] : '';
        $route = $controllerName . '/' . $actionName;

        // Preserve current context state
        $oldGet     = $_GET;
        $oldRequest = $_REQUEST;
        $oldTplName = $view->getTemplateName();
        if ($response) {
            $oldContent = $response->getContent();
        }

        // Set plugin parameters as "virtual" GET/REQUEST parameters
        $_GET = $_REQUEST = $params;

        $view->setTemplateName('');
        if ($response) {
            $response->setContent(null);
        }

        // Execute the action
        \Ptf\Core\Router::matchRoute($route, $context);

        $result = '';
        if ($response && $response->hasContent()) {
            $result = $response->getContent();
        } elseif ($view->getTemplateName() != '') {
            $result = $view->fetch();
        }

        // Restore previous context state
        $_GET     = $oldGet;
        $_REQUEST = $oldRequest;
        $view->setTemplateName($oldTplName);
        if ($response) {
            $response->setContent($oldContent);
        }

        return $result;
    }
}

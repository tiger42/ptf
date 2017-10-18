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
        $view->registerFunctionPlugin('exec', [__CLASS__, 'exec']);

        FunctionsPagination::register($view);
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
     * @param   array $params                        Parameters for the plugin
     * @param   \Smarty_Internal_Template $template  The Smarty template object
     * @return  string                               The content of the Response object, if set
     */
    public static function exec(array $params, \Smarty_Internal_Template $template)
    {
        /* @var $context \Ptf\App\Context */
        $context = $template->getTemplateVars('context');
        /* @var $view \Ptf\View\Smarty */
        $view = $context->getView();
        $response = $context->getResponse();

        $controllerName = $params['controller'] ?? '';
        $actionName     = $params['action'] ?? '';
        $route = $controllerName . '/' . $actionName;

        // Overwrite internal Smarty object with current template object
        $view->_setSmartyObject($template);

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
        $_GET = $oldGet;
        $_REQUEST = $oldRequest;
        $view->setTemplateName($oldTplName);
        if ($response) {
            $response->setContent($oldContent);
        }

        return $result;
    }
}

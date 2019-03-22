<?php

namespace Ptf\View\Plugin\Smarty;

use Ptf\View\Smarty as View;
use Smarty_Internal_Template as Smarty;

/**
 * Smarty template function plugins.
 */
class Functions
{
    /**
     * Register all Smarty function plugins of this class.
     *
     * @param View $view  The Smarty view object
     */
    public static function register(View $view): void
    {
        $view->registerFunctionPlugin('sid', [__CLASS__, 'sid']);
        $view->registerFunctionPlugin('exec', [__CLASS__, 'exec']);

        FunctionsPagination::register($view);
    }

    /**
     * Get the value of the SID constant.
     *
     * @return string  The SID constant
     */
    public static function sid(): string
    {
        return defined('SID') ? SID : '';
    }

    /**
     * Execute the given controller action.
     *
     * <pre>
     * Available parameters:
     *   controller  The controller containing the action to execute
     *   action      The action to execute
     * Any additional parameters will be set as "virtual" GET/REQUEST parameters
     * </pre>
     *
     * @param array  $params    Parameters for the plugin
     * @param Smarty $template  The Smarty template object
     *
     * @return string  The content of the Response object, if set
     */
    public static function exec(array $params, Smarty $template): string
    {
        /** @var \Ptf\App\Context $context */
        $context = $template->getTemplateVars('context');
        /** @var View $view */
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

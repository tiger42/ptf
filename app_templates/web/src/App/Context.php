<?php

namespace _NAMESPACE_\App;

/**
 * The application's context
 */
class Context extends \Ptf\App\Context
{
    /**
     * Initialize the application specific settings
     */
    protected function init()
    {
        // Initialize the view to use
        $this->view = new \Ptf\View\Plain($this->getConfig('ViewPlain'));   // You may omit this line, the Plain view is used as a fallback anyway
    }

    /**
     * Get the application's namespace
     *
     * @return  string                      The namespace of the application
     */
    public function getAppNamespace()
    {
        return '_NAMESPACE_';
    }

    /**
     * Get the name of the default controller
     *
     * @return  string                      The name of the default controller
     */
    public function getDefaultControllerName()
    {
        return 'Show';
    }
}

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

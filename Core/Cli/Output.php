<?php

namespace Ptf\Core\Cli;

/**
 * Command line output abstraction class
 */
class Output
{
    /**
     * The content to display
     * @var string
     */
    protected $content;

    /**
     * Initialize the member variables
     */
    public function __construct()
    {
        $this->content = null;
    }

    /**
     * Set the content to display
     *
     * @param   string $content             The content to set
     * @return  \Ptf\Core\Cli\Output        The output object (for fluent interface)
     */
    public function setContent($content)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * Get the content which will be displayed
     *
     * @return  string                      The content to be displayed
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Return whether any content has been set
     *
     * @return  boolean                     Has the content been set?
     */
    public function hasContent()
    {
        return $this->content !== null;
    }

    /**
     * Display the content
     *
     * @return  \Ptf\Core\Cli\Output        The output object (for fluent interface)
     */
    public function display()
    {
        echo $this->content;

        return $this;
    }

}

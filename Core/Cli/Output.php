<?php

namespace Ptf\Core\Cli;

/**
 * Command line output abstraction class.
 */
class Output
{
    /**
     * The content to display
     * @var string
     */
    protected $content;

    /**
     * Initialize the member variables.
     */
    public function __construct()
    {
        $this->content = null;
    }

    /**
     * Set the content to display.
     *
     * @param string $content  The content to set
     *
     * @return Output  The output object (for fluent interface)
     */
    public function setContent(string $content): Output
    {
        $this->content = $content;

        return $this;
    }

    /**
     * Get the content which will be displayed.
     *
     * @return string  The content to be displayed
     */
    public function getContent(): ?string
    {
        return $this->content;
    }

    /**
     * Return whether any content has been set.
     *
     * @return bool  Has the content been set?
     */
    public function hasContent(): bool
    {
        return $this->content !== null;
    }

    /**
     * Display the content.
     *
     * @return Output  The output object (for fluent interface)
     */
    public function display(): Output
    {
        echo $this->content;

        return $this;
    }
}

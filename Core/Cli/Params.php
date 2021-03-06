<?php

namespace Ptf\Core\Cli;

/**
 * Abstraction class for the command line parameters.
 */
class Params
{
    /** @var array  Array holding the CLI parameters */
    protected $parameters;

    /**
     * Initialize the member variables.
     */
    public function __construct()
    {
        $this->init();
    }

    /**
     * Get all CLI parameters as an array.
     *
     * @return array  All set CLI parameters
     */
    public function getAll(): array
    {
        return $this->parameters;
    }

    /**
     * Get the value of the given parameter.
     *
     * @param string $name  The name of the parameter to get the value of
     *
     * @return string  The value of the parameter
     */
    public function get(string $name): ?string
    {
        return $this->parameters[$name] ?? null;
    }

    /**
     * Check whether the parameter with the given name has been set.
     *
     * @param string $name  The name of the parameter to check
     *
     * @return bool  Has the parameter been set?
     */
    public function has(string $name): bool
    {
        return array_key_exists($name, $this->parameters);
    }

    /**
     * Initialize the internal parameters array.
     */
    protected function init(): void
    {
        $this->parameters = [];

        $i = 1;
        while ($i < $_SERVER['argc']) {
            $parts = explode('=', $_SERVER['argv'][$i], 2);

            $paramName = $parts[0];
            $value = null;

            if (isset($parts[1])) {
                $value = $parts[1];
            } elseif ($parts[0][0] == '-'
                && isset($_SERVER['argv'][$i + 1]) && $_SERVER['argv'][$i + 1][0] != '-'
            ) {
                $value = $_SERVER['argv'][$i + 1];
                $i++;
            }
            $i++;

            $this->parameters[$paramName] = $value;
        }
    }
}

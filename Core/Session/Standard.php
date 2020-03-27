<?php

namespace Ptf\Core\Session;

/**
 * Wrapper for the standard PHP session handling (configured in php.ini).
 */
class Standard extends \Ptf\Core\Session
{
    /**
     * Override the save handler registration of parent.
     */
    protected function setSaveHandler(): void
    {
    }

    /**
     * Dummy function.
     *
     * @param string $id
     *
     * @return string
     */
    public function readSession(string $id): string
    {
        return '';
    }

    /**
     * Dummy function.
     *
     * @param string $id
     * @param string $data
     *
     * @return bool
     */
    public function writeSession(string $id, string $data): bool
    {
        return true;
    }

    /**
     * Dummy function.
     *
     * @param string $id
     *
     * @return bool
     */
    public function destroySession(string $id): bool
    {
        return true;
    }

    /**
     * Dummy function.
     *
     * @param int $maxLifetime
     *
     * @return bool
     */
    public function gcSession(int $maxLifetime): bool
    {
        return true;
    }
}

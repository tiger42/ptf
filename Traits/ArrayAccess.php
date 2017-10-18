<?php

namespace Ptf\Traits;

/**
 * Trait for classes that implement the ArrayAccess interface.
 */
trait ArrayAccess
{
    /**
     * Return whether the given offset exists.
     *
     * @param string $offset  The offset to check
     *
     * @return bool  Does the offset exist?
     */
    public function offsetExists($offset)
    {
        return isset($this->$offset);
    }

    /**
     * Retrieve the value at the given offset.
     *
     * @param string $offset  The offset to get the value for
     *
     * @return mixed  The value at the given offset
     */
    public function offsetGet($offset)
    {
        return $this->$offset;
    }

    /**
     * Set the value of the given offset.
     *
     * @param string $offset  The offset to set the value of
     * @param string $value   The value to set
     */
    public function offsetSet($offset, $value)
    {
        $this->$offset = $value;
    }

    /**
     * Delete the the given offset.
     *
     * @param string $offset  The offset to delete
     */
    public function offsetUnset($offset)
    {
        unset($this->$offset);
    }
}

<?php

namespace Horde\Support;

use ArrayAccess;
use Countable;
use IteratorAggregate;
use InvalidArgumentException;
use Traversable;
use ArrayIterator;

/**
 * Copyright 2007-2017 Horde LLC (http://www.horde.org/)
 *
 * @todo - Incorporate stuff from Horde_Array?
 *       - http://docs.python.org/lib/typesmapping.html
 *
 * @category   Horde
 * @package    Support
 * @license    http://www.horde.org/licenses/bsd
 */
class HordeArray implements ArrayAccess, Countable, IteratorAggregate
{
    /**
     * Array variables
     */
    protected $_array = [];

    /**
     */
    public function __construct($vars = [])
    {
        if (is_array($vars)) {
            $this->update($vars);
        }
    }

    /**
     */
    public function get($key, $default = null)
    {
        return $this->_array[$key] ?? $default;
    }

    /**
     * Gets the value at $offset. If no value exists at that offset, or the
     * value $offset is NULL, then $default is set as the value of $offset.
     *
     * @param string $offset   Offset to retrieve and set if unset
     * @param string $default  Default value if $offset does not exist
     *
     * @return mixed Value at $offset or $default
     */
    public function getOrSet($offset, $default = null)
    {
        $value = $this->offsetGet($offset);
        if (is_null($value)) {
            $this->offsetSet($offset, $value = $default);
        }
        return $value;
    }

    /**
     * Gets the value at $offset and deletes it from the array. If no value
     * exists at $offset, or the value at $offset is null, then $default
     * will be returned.
     *
     * @param string $offset   Offset to pop
     * @param string $default  Default value
     *
     * @return mixed Value at $offset or $default
     */
    public function pop($offset, $default = null)
    {
        $value = $this->offsetGet($offset);
        $this->offsetUnset($offset);
        return $value ?? $default;
    }

    /**
     * Update the array with the key/value pairs from $array
     *
     * @param array|Traversable $array Key/value pairs to set/change in the array.
     */
    public function update($array)
    {
        if (!is_array($array) && !$array instanceof Traversable) {
            throw new InvalidArgumentException('expected array or traversable, got ' . gettype($array));
        }

        foreach ($array as $key => $val) {
            $this->offsetSet($key, $val);
        }
    }

    /**
     * Get the keys in the array
     *
     * @return array
     */
    public function getKeys()
    {
        return array_keys($this->_array);
    }

    /**
     * Get the values in the array
     *
     * @return array
     */
    public function getValues()
    {
        return array_values($this->_array);
    }

    /**
     * Clear out the array
     */
    public function clear()
    {
        $this->_array = [];
    }

    /**
     */
    public function __get($key)
    {
        return $this->get($key);
    }

    /**
     */
    public function __set($key, $value): void
    {
        $this->_array[$key] = $value;
    }

    /**
     * Checks the existance of $key in this array
     */
    public function __isset($key): bool
    {
        return array_key_exists($key, $this->_array);
    }

    /**
     * Removes $key from this array
     */
    public function __unset($key): void
    {
        unset($this->_array[$key]);
    }

    /**
     * Count the number of elements
     *
     * @return integer
     */
    public function count(): int
    {
        return count($this->_array);
    }

    /**
     */
    public function getIterator(): ArrayIterator
    {
        return new ArrayIterator($this->_array);
    }

    /**
     * Gets the value of $offset in this array
     *
     * @see __get()
     */
    public function offsetGet($offset)
    {
        return $this->__get($offset);
    }

    /**
     * Sets the value of $offset to $value
     *
     * @see __set()
     */
    public function offsetSet($offset, $value): void
    {
        $this->__set($offset, $value);
    }

    /**
     * Checks the existence of $offset in this array
     *
     * @see __isset()
     */
    public function offsetExists($offset): bool
    {
        return $this->__isset($offset);
    }

    /**
     * Removes $offset from this array
     *
     * @see __unset()
     */
    public function offsetUnset($offset): void
    {
        $this->__unset($offset);
    }
}

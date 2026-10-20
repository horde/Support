<?php

/**
 * Copyright 2013-2026 Horde LLC (http://www.horde.org/)
 *
 * See the enclosed file LICENSE for license information (BSD). If you
 * did not receive this file, see http://www.horde.org/licenses/bsd.
 *
 * @category  Horde
 * @copyright 2013-2026 The Horde Project
 * @license   http://www.horde.org/licenses/bsd BSD
 * @package   Support
 */

namespace Horde\Support;

use ArrayIterator;

/**
 * An array implemented as an object that contains case-insensitive keys.
 *
 * @author    Michael Slusarz <slusarz@horde.org>
 * @category  Horde
 * @copyright 2013-2026 Horde LLC
 * @license   http://www.horde.org/licenses/bsd BSD
 * @package   Support
 */
class CaseInsensitiveArray extends ArrayIterator
{
    /**
     * Maps lowercased keys to their real (originally-cased) counterparts.
     * Makes case-insensitive lookups O(1) instead of a linear scan.
     *
     * @var array<string, string>
     */
    protected array $_caseMap = [];

    /**
     */
    public function __construct($array = [], $flags = 0)
    {
        parent::__construct($array, $flags);

        /* @performance PHP 8.4 In testing with 5-digit arrays, array_keys wins over foreach $array as $key => $value. */
        foreach (array_keys($array) as $key) {
            $this->_caseMap[strtolower($key)] = $key;
        }
    }

    /**
     * ArrayIterator's internal storage is not exposed as a regular
     * property. PHP's default (shallow) clone does not duplicate it.
     * Rebuild storage explicitly so clones are independent.
     */
    public function __clone(): void
    {
        parent::__construct($this->getArrayCopy(), $this->getFlags());
    }

    /**
     */
    public function offsetGet($offset): mixed
    {
        return (is_null($offset = $this->_getRealOffset($offset)))
            ? null
            : parent::offsetGet($offset);
    }

    /**
     */
    public function offsetSet($offset, $value): void
    {
        if (is_null($roffset = $this->_getRealOffset($offset))) {
            parent::offsetSet($offset, $value);
            $this->_caseMap[strtolower($offset)] = $offset;
        } else {
            parent::offsetSet($roffset, $value);
        }
    }

    /**
     */
    public function offsetExists($offset): bool
    {
        return !is_null($offset = $this->_getRealOffset($offset));
    }

    /**
     */
    public function offsetUnset($offset): void
    {
        if (!is_null($offset = $this->_getRealOffset($offset))) {
            parent::offsetUnset($offset);
            unset($this->_caseMap[strtolower($offset)]);
        }
    }

    /**
     * Determines the actual array offset given the input offset.
     *
     * @param string $offset  Input offset.
     *
     * @return string|null  Real offset or null.
     */
    protected function _getRealOffset($offset)
    {
        /* Optimize: check for base $offset in array first. */
        if (parent::offsetExists($offset)) {
            return $offset;
        }

        return $this->_caseMap[strtolower($offset)] ?? null;
    }
}

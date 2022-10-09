<?php
/**
 * Copyright 2013-2017 Horde LLC (http://www.horde.org/)
 *
 * See the enclosed file LICENSE for license information (BSD). If you
 * did not receive this file, see http://www.horde.org/licenses/bsd.
 *
 * @category  Horde
 * @copyright 2013-2017 Horde LLC
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
 * @copyright 2013-2017 Horde LLC
 * @license   http://www.horde.org/licenses/bsd BSD
 * @package   Support
 */
class CaseInsensitiveArray extends ArrayIterator
{
    /**
     */
    public function offsetGet($offset)
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

        foreach (array_keys($this->getArrayCopy()) as $key) {
            if (strcasecmp($key, $offset) === 0) {
                return $key;
            }
        }

        return null;
    }
}

<?php

namespace Horde\Support;

/**
 * Class for generating GUIDs. Usage:
 *
 * <code>
 * $uid = (string)new Horde_Support_Guid([$opts = array()]);
 * </code>
 *
 * Copyright 2009-2017 Horde LLC (http://www.horde.org/)
 *
 * @category   Horde
 * @package    Support
 * @license    http://www.horde.org/licenses/bsd
 */
class Guid
{
    /**
     * Generated GUID.
     *
     * @var string
     */
    private $_guid;

    /**
     * New GUID.
     *
     * @param array $opts  Additional options:
     * <pre>
     * 'prefix' - (string) A prefix to add between the date string and the
     *            random string.
     *            DEFAULT: NONE
     * 'server' - (string) The server name.
     *            DEFAULT: $_SERVER['SERVER_NAME'] (or 'localhost')
     * </pre>
     */
    public function __construct(array $opts = [])
    {
        $this->generate($opts);
    }

    /**
     * Generates a GUID.
     *
     * @param array $opts  Additional options:
     * <pre>
     * 'prefix' - (string) A prefix to add between the date string and the
     *            random string.
     *            DEFAULT: NONE
     * 'server' - (string) The server name.
     *            DEFAULT: $_SERVER['SERVER_NAME'] (or 'localhost')
     * </pre>
     */
    public function generate(array $opts = [])
    {
        $this->_guid = date('YmdHis')
            . '.'
            . (isset($opts['prefix']) ? $opts['prefix'] . '.' : '')
            . strval(new Randomid())
            . '@'
            . ($opts['server'] ?? ($_SERVER['SERVER_NAME'] ?? 'localhost'));
    }

    /**
     * Cooerce to string.
     *
     * @return string
     */
    public function __toString()
    {
        return $this->_guid;
    }
}

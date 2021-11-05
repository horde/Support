<?php

namespace Horde\Support;

/**
 * Class for generating RFC 4122 UUIDs. Usage:
 *
 * <code>
 * $uuid = (string)new Horde_Support_Uuid;
 * </code>
 *
 * Copyright 2008-2017 Horde LLC (http://www.horde.org/)
 *
 * @category   Horde
 * @package    Support
 * @license    http://www.horde.org/licenses/bsd
 */
class Uuid
{
    /**
     * Generated UUID
     * @var string|null
     */
    private $_uuid;

    /**
     * New UUID.
     */
    public function __construct()
    {
        $this->generate();
    }

    /**
     * Generate a 36-character RFC 4122 UUID, without the urn:uuid: prefix.
     *
     * @see http://www.ietf.org/rfc/rfc4122.txt
     * @see http://labs.omniti.com/alexandria/trunk/OmniTI/Util/UUID.php
     */
    public function generate()
    {
        $this->_uuid = null;
        if (extension_loaded('uuid')) {
            // This used to have support for both the OSSP UUID package and pecl UUID
            // However, since that package is not available for PHP7/8, I dropped that code
            // UUID extension from http://pecl.php.net/package/uuid
            $this->_uuid = uuid_create();
        }
        if (!$this->_uuid) {
            [$time_mid, $time_low] = explode(' ', microtime());
            $time_low = (int)$time_low;
            $time_mid = (int)substr($time_mid, 2) & 0xffff;
            $time_high = mt_rand(0, 0x0fff) | 0x4000;

            $clock = mt_rand(0, 0x3fff) | 0x8000;

            $node_low = function_exists('zend_thread_id')
                ? zend_thread_id()
                : getmypid();
            $node_high = isset($_SERVER['SERVER_ADDR'])
                ? ip2long($_SERVER['SERVER_ADDR'])
                : crc32(php_uname());
            $node = bin2hex(pack('nN', $node_low, $node_high));

            $this->_uuid = sprintf(
                '%08x-%04x-%04x-%04x-%s',
                $time_low,
                $time_mid,
                $time_high,
                $clock,
                $node
            );
        }
    }

    /**
     * Cooerce to string.
     *
     * @return string  UUID.
     */
    public function __toString()
    {
        return $this->_uuid;
    }
}

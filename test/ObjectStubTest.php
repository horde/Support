<?php
/**
 * Copyright 2012-2017 Horde LLC (http://www.horde.org/)
 *
 * @category   Horde
 * @package    Support
 * @subpackage UnitTests
 * @license    http://www.horde.org/licenses/bsd
 */
namespace Horde\Support\Test;
use PHPUnit\Framework\TestCase;
use \Horde_Support_ObjectStub;
use \stdClass;

/**
 * @category   Horde
 * @package    Support
 * @subpackage UnitTests
 * @license    http://www.horde.org/licenses/bsd
 */
class ObjectStubTest extends TestCase
{
    public function testUndefinedIndex()
    {
        $stub = new Horde_Support_ObjectStub(new stdClass);
        unset($php_errormsg);
        $oldTrackErrors = ini_set('track_errors', 1);
        $php_errormsg = null;
        $this->assertNull($stub->a);
        $this->assertNull($php_errormsg);
        ini_set('track_errors', $oldTrackErrors);
    }
}

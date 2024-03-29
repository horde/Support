<?php
/**
 * Copyright 2010-2017 Horde LLC (http://www.horde.org/)
 *
 * @category   Horde
 * @package    Support
 * @subpackage UnitTests
 * @license    http://www.horde.org/licenses/bsd
 */
namespace Horde\Support\Test;
use PHPUnit\Framework\TestCase;
use \Horde_Support_Guid;

/**
 * @category   Horde
 * @package    Support
 * @subpackage UnitTests
 * @license    http://www.horde.org/licenses/bsd
 */
class GuidTest extends TestCase
{
    public function testFormat()
    {
        $guid = new Horde_Support_Guid(array('server' => 'localhost'));
        $this->assertEquals(48, strlen($guid));
        $this->assertMatchesRegularExpression('/\d{14}\.[-_0-9a-zA-Z]{23}@localhost/', (string)$guid);
    }

    public function testDuplicates()
    {
        $values = array();

        for ($i = 0; $i < 10000; ++$i) {
            $id = strval(new Horde_Support_Guid());
            $this->assertArrayNotHasKey($id, $values);
            $values[$id] = 1;
        }
    }

    public function testOptions()
    {
        $this->assertStringEndsWith('example.com', (string)new Horde_Support_Guid(array('server' => 'example.com')));
        $this->assertMatchesRegularExpression('/\d{14}\.prefix\.[-_0-9a-zA-Z]{23}@localhost/', (string)new Horde_Support_Guid(array('prefix' => 'prefix', 'server' => 'localhost')));
    }
}

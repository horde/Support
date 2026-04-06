<?php

/**
 * Copyright 2010-2026 Horde LLC (http://www.horde.org/)
 * @category   Horde
 * @package    Support
 * @subpackage UnitTests
 * @license    http://www.horde.org/licenses/bsd
 */

namespace Horde\Support\Test;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use Horde_Support_Uuid;

/**
 * @category   Horde
 * @package    Support
 * @subpackage UnitTests
 * @license    http://www.horde.org/licenses/bsd
#[CoversClass(Horde_Support_Uuid::class)]
 * @coversNothing
 */
class UuidTest extends TestCase
{
    public function testLength()
    {
        $this->assertEquals(36, strlen(new Horde_Support_Uuid()));
    }

    public function testDuplicates()
    {
        $values = [];

        for ($i = 0; $i < 10000; ++$i) {
            $id = strval(new Horde_Support_Uuid());
            $this->assertArrayNotHasKey($id, $values);
            $values[$id] = 1;
        }
    }
}

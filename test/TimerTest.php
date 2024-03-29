<?php
/**
 * Copyright 2009-2017 Horde LLC (http://www.horde.org/)
 *
 * @category   Horde
 * @package    Support
 * @subpackage UnitTests
 * @license    http://www.horde.org/licenses/bsd
 */
namespace Horde\Support\Test;
use PHPUnit\Framework\TestCase;
use Horde_Support_Timer;

/**
 * @category   Horde
 * @package    Support
 * @subpackage UnitTests
 * @license    http://www.horde.org/licenses/bsd
 */
class TimerTest extends TestCase
{
    /**
     * test instantiating a normal timer
     */
    public function testNormalTiming()
    {
        $t = new Horde_Support_Timer;
        $start = $t->push();
        $elapsed = $t->pop();

        $this->assertTrue(is_float($start));
        $this->assertTrue(is_float($elapsed));
        //$this->assertTrue($elapsed > 0);
    }

    /**
     * test getting the finish time before starting the timer
     */
    public function testNotStartedYetThrowsException()
    {
        $this->expectException('Exception');
        $t = new Horde_Support_Timer();
        $t->pop();
    }

}

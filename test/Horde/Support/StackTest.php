<?php
/**
 * Copyright 2007-2017 Horde LLC (http://www.horde.org/)
 *
 * @category   Horde
 * @package    Support
 * @subpackage UnitTests
 * @license    http://www.horde.org/licenses/bsd
 */

/**
 * @category   Horde
 * @package    Support
 * @subpackage UnitTests
 * @license    http://www.horde.org/licenses/bsd
 */
class Horde_Support_StackTest extends Horde_Test_Case
{
    public function testEmptyConstructor()
    {
        $this->expectNotToPerformAssertions();

        return new Horde_Support_Stack();
    }

    /**
     * @depends testEmptyConstructor
     */
    public function testPushOnEmptyStack($stack)
    {
        $this->expectNotToPerformAssertions();

        $stack->push('one');
        $stack->push('two');
        return $stack;
    }

    /**
     * @depends testPushOnEmptyStack
     */
    public function testPeekOnEmptyStack($stack)
    {
        $this->assertEquals('two', $stack->peek());
        $this->assertEquals('two', $stack->peek(1));
        $this->assertEquals('one', $stack->peek(2));
        $this->assertNull($stack->peek(3));
        $this->assertNull($stack->peek(0));
    }

    /**
     * @depends testPushOnEmptyStack
     */
    public function testPopFromEmptyStack($stack)
    {
        $this->assertEquals('two', $stack->pop());
        $this->assertEquals('one', $stack->pop());
        $this->assertNull($stack->pop());
    }

    public function testPrefilledConstructor()
    {
        $this->expectNotToPerformAssertions();

        return new Horde_Support_Stack(array('foo', 'bar'));
    }

    /**
     * @depends testPrefilledConstructor
     */
    public function testPeekOnPrefilledStack($stack)
    {
        $this->assertEquals('bar', $stack->peek(1));
        $this->assertEquals('foo', $stack->peek(2));
    }

    /**
     * @depends testPrefilledConstructor
     */
    public function testPushOnPrefilledStack($stack)
    {
        $this->expectNotToPerformAssertions();

        $stack->push('baz');
        return $stack;
    }

    /**
     * @depends testPushOnPrefilledStack
     */
    public function testPopFromPrefilledStack($stack)
    {
        $this->assertEquals('baz', $stack->pop());
        $this->assertEquals('bar', $stack->pop());
        $this->assertEquals('foo', $stack->pop());
        $this->assertNull($stack->pop());
    }
}

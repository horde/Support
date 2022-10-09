<?php
/**
 * Copyright 2007-2017 Horde LLC (http://www.horde.org/)
 *
 * @category   Horde
 * @package    Support
 * @subpackage UnitTests
 * @license    http://www.horde.org/licenses/bsd
 */
namespace Horde\Support\Test;
use PHPUnit\Framework\TestCase;
use Horde_Support_Stack;

/**
 * @category   Horde
 * @package    Support
 * @subpackage UnitTests
 * @license    http://www.horde.org/licenses/bsd
 */
class StackTest extends TestCase
{
    protected Horde_Support_Stack $prefilledStack;
    protected Horde_Support_Stack $stack;

    public function testEmptyConstructor()
    {
        $this->assertInstanceOf('Horde_Support_Stack', new Horde_Support_Stack());
    }

    public function setUp(): void
    {
        $this->stack = new Horde_Support_Stack();
        $this->prefilledStack = new Horde_Support_Stack(['foo', 'bar']);
    }

    public function testPushOnEmptyStack()
    {
        $this->stack->push('one');
        $this->stack->push('two');
        $this->assertEquals('two', $this->stack->peek(1), 'Looking up first element on stack');
        $this->assertEquals(null, $this->stack->peek(3));
    }

    public function testPeekOnEmptyStack()
    {
        $this->stack->push('one');
        $this->stack->push('two');
        $this->assertEquals('two', $this->stack->peek());
        $this->assertEquals('two', $this->stack->peek(1));
        $this->assertEquals('one', $this->stack->peek(2));
        $this->assertNull($this->stack->peek(3));
        $this->assertNull($this->stack->peek(0));
    }

    public function testPopFromEmptyStack()
    {
        $this->stack->push('one');
        $this->stack->push('two');
        $this->assertEquals('two', $this->stack->pop());
        $this->assertEquals('one', $this->stack->pop());
        $this->assertNull($this->stack->pop());
    }

    public function testPrefilledConstructor()
    {
        $this->assertInstanceOf('Horde_Support_Stack', $this->prefilledStack);
    }

    public function testPeekOnPrefilledStack()
    {
        $stack = $this->prefilledStack;
        $this->assertEquals('bar', $stack->peek(1));
        $this->assertEquals('foo', $stack->peek(2));
    }

    public function testPushOnPrefilledStack()
    {
        $stack = $this->prefilledStack;
        $stack->push('baz');
        $this->assertEquals('baz', $stack->peek(1));
        $this->assertEquals('foo', $stack->peek(3));
    }

    public function testPopFromPrefilledStack()
    {
        $stack = $this->prefilledStack;
        $stack->push('baz');
        $this->assertEquals('baz', $stack->pop());
        $this->assertEquals('bar', $stack->pop());
        $this->assertEquals('foo', $stack->pop());
        $this->assertNull($stack->pop());
    }
}

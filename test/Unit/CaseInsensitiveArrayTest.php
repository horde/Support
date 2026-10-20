<?php

/**
 * Copyright 2013-2026 The Horde Project (http://www.horde.org/)
 *
 * @category   Horde
 * @internal
 * @license    http://www.horde.org/licenses/bsd
 * @package    Support
 * @subpackage UnitTests
 * @copyright 2013-2026 The Horde Project
 */

namespace Horde\Support\Test;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use Horde_Support_CaseInsensitiveArray;
use Horde\Support\CaseInsensitiveArray;
use ArrayIterator;

/**
 * Exercises both the PSR-0 legacy class (Horde_Support_CaseInsensitiveArray)
 * and the PSR-4 class (Horde\Support\CaseInsensitiveArray) with the same
 * test logic. They should only differ by legacy signatures.
 *
 * @category   Horde
 * @internal
 * @license    http://www.horde.org/licenses/bsd
 * @package    Support
 * @subpackage UnitTests
 */
#[CoversClass(Horde_Support_CaseInsensitiveArray::class)]
#[CoversClass(CaseInsensitiveArray::class)]
class CaseInsensitiveArrayTest extends TestCase
{
    public static function classProvider()
    {
        return [
            'legacy (PSR-0)' => [Horde_Support_CaseInsensitiveArray::class],
            'PSR-4' => [CaseInsensitiveArray::class],
        ];
    }

    /**
     * @return ArrayIterator
     */
    protected function make(string $class, array $array = [])
    {
        return new $class($array);
    }

    #[DataProvider('classProvider')]
    public function testImplementsArrayAccess(string $class)
    {
        $this->assertInstanceOf('ArrayAccess', $this->make($class));
    }

    #[DataProvider('classProvider')]
    public function testImplementsTraversable(string $class)
    {
        $this->assertInstanceOf('Traversable', $this->make($class));
    }

    #[DataProvider('classProvider')]
    public function testImplementsCountable(string $class)
    {
        $this->assertInstanceOf('Countable', $this->make($class));
    }

    #[DataProvider('classProvider')]
    public function testOffsetGetReturnsValueAtOffset(string $class)
    {
        $o = $this->make($class, ['foo' => 'bar']);
        $this->assertEquals('bar', $o['foo']);
    }

    #[DataProvider('classProvider')]
    public function testOffsetGetReturnsNullWhenOffsetDoesNotExist(string $class)
    {
        $o = $this->make($class);
        $this->assertNull($o['foo']);
    }

    #[DataProvider('classProvider')]
    public function testCaseInsensitiveKeys(string $class)
    {
        $o = $this->make($class, ['foo' => 'bar']);

        $this->assertTrue(isset($o['foo']));
        $this->assertTrue(isset($o['Foo']));
        $this->assertTrue(isset($o['FOO']));

        $this->assertEquals('bar', $o['foo']);
        $this->assertEquals('bar', $o['Foo']);
        $this->assertEquals('bar', $o['FOO']);

        unset($o['FOO']);

        $this->assertFalse(isset($o['foo']));
    }

    #[DataProvider('classProvider')]
    public function testOverwriteExistingKeyWithDifferentCaseUpdatesValueOnly(string $class)
    {
        $o = $this->make($class, ['foo' => 'bar']);
        $o['FOO'] = 'baz';

        $this->assertEquals('baz', $o['foo']);
        $this->assertEquals(['foo' => 'baz'], $o->getArrayCopy());
    }

    #[DataProvider('classProvider')]
    public function testUnsetThenSetWithDifferentCaseResolvesToNewCasing(string $class)
    {
        $o = $this->make($class, ['foo' => 'bar']);
        unset($o['foo']);
        $o['Foo'] = 'baz';

        $this->assertTrue(isset($o['FOO']));
        $this->assertEquals('baz', $o['foo']);
        $this->assertEquals(['Foo' => 'baz'], $o->getArrayCopy());
    }

    #[DataProvider('classProvider')]
    public function testConstructingFromPrepopulatedArrayResolvesCaseInsensitively(string $class)
    {
        $o = $this->make($class, ['Foo' => 'bar', 'Baz' => 'qux']);

        $this->assertEquals('bar', $o['foo']);
        $this->assertEquals('bar', $o['FOO']);
        $this->assertEquals('qux', $o['baz']);
        $this->assertEquals(['Foo' => 'bar', 'Baz' => 'qux'], $o->getArrayCopy());
    }

    #[DataProvider('classProvider')]
    public function testInsertCostScalesLinearlyNotQuadratically(string $class)
    {
        $populate = function (int $n) use ($class) {
            $o = $this->make($class);
            for ($i = 0; $i < $n; $i++) {
                $o['key' . $i] = $i;
            }
            return $o;
        };

        // Warm up (autoloading, opcache, etc.) before timing.
        $populate(2000);

        $n = 1500;
        $start = microtime(true);
        $populate($n);
        $timeN = microtime(true) - $start;

        $start = microtime(true);
        $populate($n * 4);
        $time4N = microtime(true) - $start;

        // O(N) insertion should yield time(4N) ~= 4x time(N). O(N^2)
        // insertion would yield ~16x. Compensate for timing
        // noise but catch any regression back to quadratic cost.
        $this->assertLessThan(
            $timeN * 6,
            $time4N,
            'Insert cost appears quadratic: time(4N)=' . $time4N
                . 's is not within 6x of time(N)=' . $timeN . 's'
        );
    }

    #[DataProvider('classProvider')]
    public function testClonedArrayIsIndependentOfOriginal(string $class)
    {
        $o = $this->make($class, ['Foo' => 'bar']);
        $c = clone $o;

        // Mutating one must not affect the other's backing storage.
        $c['NewKey'] = 'baz';
        $o['foo'] = 'changed';

        $this->assertEquals(['Foo' => 'changed'], $o->getArrayCopy());
        $this->assertEquals(['Foo' => 'bar', 'NewKey' => 'baz'], $c->getArrayCopy());

        // Case-insensitive resolution must still work on the clone,
        // including for keys added only after cloning.
        $this->assertTrue(isset($c['newkey']));
        $this->assertFalse(isset($o['newkey']));
        $this->assertEquals('bar', $c['foo']);
    }

}

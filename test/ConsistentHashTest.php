<?php
/**
 * Copyright 2008-2017 Horde LLC (http://www.horde.org/)
 *
 * @category   Horde
 * @package    Support
 * @subpackage UnitTests
 * @license    http://www.horde.org/licenses/bsd
 */
namespace Horde\Support\Test;
use PHPUnit\Framework\TestCase;
use \Horde\Support\Test\Helper\ConsistentHashInstrumented;
use \InvalidArgumentException;

/**
 * @category   Horde
 * @package    Support
 * @subpackage UnitTests
 * @license    http://www.horde.org/licenses/bsd
 */
class ConsistentHashTest extends TestCase
{
    public function testAddUpdatesCount()
    {
        $h = new ConsistentHashInstrumented;
        $this->assertEquals(0, $h->exposeNodeCount());

        $h->add('a');
        $this->assertEquals(1, $h->exposeNodeCount());
        $this->assertEquals(count($h->exposeNodes()), $h->exposeNodeCount());
    }

    public function testAddUpdatesPointCount()
    {
        $numberOfReplicas = 100;
        $h = new ConsistentHashInstrumented([], 1, $numberOfReplicas);
        $this->assertEquals(0, $h->exposePointCount());
        $this->assertEquals(count($h->exposeCircle()), $h->exposePointCount());
        $this->assertEquals(count($h->exposePointMap()), $h->exposePointCount());

        $h->add('a');
        $this->assertEquals(100, $h->exposePointCount());
        $this->assertEquals(count($h->exposeCircle()), $h->exposePointCount());
        $this->assertEquals(count($h->exposePointMap()), $h->exposePointCount());
    }

    public function testAddWithWeightGeneratesMorePoints()
    {
        $weight = 2;
        $numberOfReplicas = 100;
        $h = new ConsistentHashInstrumented([], 1, $numberOfReplicas);
        $this->assertEquals(0, $h->exposePointCount());
        $this->assertEquals(count($h->exposeCircle()), $h->exposePointCount());
        $this->assertEquals(count($h->exposePointMap()), $h->exposePointCount());

        $h->add('a', $weight);
        $this->assertEquals($numberOfReplicas * $weight, $h->exposePointCount());
        $this->assertEquals(count($h->exposeCircle()), $h->exposePointCount());
        $this->assertEquals(count($h->exposePointMap()), $h->exposePointCount());
    }

    public function testRemoveRemovesPoints()
    {
        $h = new ConsistentHashInstrumented;
        $this->assertEquals(0, $h->exposeNodeCount());

        $h->add('a');
        $h->remove('a');
        $this->assertEquals(0, $h->exposeNodeCount());
        $this->assertEquals(0, $h->exposePointCount());
        $this->assertEquals(count($h->exposeCircle()), $h->exposePointCount());
        $this->assertEquals(count($h->exposePointMap()), $h->exposePointCount());
    }

    public function testRemoveThrowsOnNonexistentNode()
    {
        $h = new ConsistentHashInstrumented;
        $this->expectException(InvalidArgumentException::class);
        $h->remove('a');
    }

    public function testLookupsReturnValidNodes()
    {
        $nodes = range(1, 10);
        $h = new ConsistentHashInstrumented($nodes);

        foreach (range(1, 10) as $i) {
            $this->assertContains($h->get($i), $nodes);
        }
    }

    public function testLookupRatiosWithDifferentNodeWeights()
    {
        $h = new ConsistentHashInstrumented;
        $h->add('a', 2);
        $h->add('b', 1);
        $h->add('c', 3);
        $h->add('d', 4);

        $choices = array('a' => 0, 'b' => 0, 'c' => 0, 'd' => 0);
        for ($i = 0; $i < 1000; $i++) {
            $choices[$h->get(uniqid(mt_rand()))]++;
        }

        // Due to randomness it's entirely possible to have some overlap in the
        // middle, but the highest-weighted node should definitely be chosen
        // more than the lowest-weighted one.
        $this->assertGreaterThan($choices['b'], $choices['d']);
    }

    public function testRepeatableLookups()
    {
        $h = new ConsistentHashInstrumented(range(1, 10));

        $this->assertEquals($h->get('t1'), $h->get('t1'));
        $this->assertEquals($h->get('t2'), $h->get('t2'));
    }

    public function testRepeatableLookupsAfterAddingAndRemoving()
    {
        $h = new ConsistentHashInstrumented(range(1, 100));

        $results1 = [];
        foreach (range(1, 100) as $i) {
            $results1[] = $h->get($i);
        }

        $h->add('new');
        $h->remove('new');
        $h->add('new');
        $h->remove('new');

        $results2 = [];
        foreach (range(1, 100) as $i) {
            $results2[] = $h->get($i);
        }

        $this->assertEquals($results1, $results2);
    }

    public function testRepeatableLookupsBetweenInstances()
    {
        $h1 = new ConsistentHashInstrumented(range(1, 10));
        $results1 = [];
        foreach (range(1, 100) as $i) {
            $results1[] = $h1->get($i);
        }

        $h2 = new ConsistentHashInstrumented(range(1, 10));
        $results2 = [];
        foreach (range(1, 100) as $i) {
            $results2[] = $h2->get($i);
        }

        $this->assertEquals($results1, $results2);
    }

    public function testGetNodes()
    {
        $h = new ConsistentHashInstrumented(range(1, 10));
        $nodes = $h->getNodes('r', 2);

        $this->assertIsArray($nodes);
        $this->assertEquals(count($nodes), 2);
        $this->assertNotEquals($nodes[0], $nodes[1]);
    }

    public function testGetNodesWithNotEnoughNodes()
    {
        $this->expectException('Exception');
        $h = new ConsistentHashInstrumented(['t']);
        $h->getNodes('resource', 2);
    }

    public function testGetNodesWrapsToBeginningOfCircle()
    {
        $h = new ConsistentHashInstrumented([], 1, 1);

        // Create an array of random values and one fixed test value and sort
        // them by their hashes
        $nodes = [];
        for ($i = 0; $i < 10; $i++) {
            $val = uniqid(mt_rand(), true);
            $nodes[$h->hash(serialize($val) . '0')] = $val;
        }
        $nodes[$h->hash(serialize('key'))] = 'key';
        ksort($nodes);

        // Remove the fixed test value.
        $nodes = array_values($nodes);
        $testindex = array_search('key', $nodes);
        array_splice($nodes, $testindex, 1);

        foreach ($nodes as $node) {
            $h->add($node);
        }

        $expected = [];
        for ($i = 0; $i < 10; $i++) {
            $expected[] = $nodes[($testindex + $i) % 10];
        }

        $this->assertEquals(
            $expected,
            $h->getNodes('key', 10));
    }

    public function testFallbackWhenANodeIsRemoved()
    {
        $h = new ConsistentHashInstrumented([], 1, 1);

        // Create an array of random values and one fixed test value and sort
        // them by their hashes
        $nodes = [];
        for ($i = 0; $i < 10; $i++) {
            $val = uniqid(mt_rand(), true);
            $nodes[$h->hash(serialize($val) . '0')] = $val;
        }
        $nodes[$h->hash(serialize('key'))] = 'key';
        ksort($nodes);

        // Remove the fixed test value.
        $nodes = array_values($nodes);
        $testindex = array_search('key', $nodes);
        array_splice($nodes, $testindex, 1);

        foreach ($nodes as $node) {
            $h->add($node);
        }

        $this->assertEquals($h->get('key'), $nodes[$testindex]);

        $h->remove($nodes[$testindex]);
        $this->assertEquals($h->get('key'), $nodes[($testindex + 1) % 10]);

        $h->remove($nodes[($testindex + 1) % 10]);
        $this->assertEquals($h->get('key'), $nodes[($testindex + 2) % 10]);
    }

}

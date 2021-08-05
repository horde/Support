<?php
/**
 * Expose select internal state for unit tests
 *
 * PHPUnit has removed capability to read internal attributes
 * ConsistentHash does not expose the NodeCount though
 * Maybe we could find another way to observe correctness
 *
 * @author Ralf Lang <lang@b1-systems.de>
 */
declare(strict_types=1);
namespace Horde\Support\Test\Helper;
use \Horde_Support_ConsistentHash as ConsistentHash;

class ConsistentHashInstrumented extends ConsistentHash
{
    public function exposeNodeCount(): int
    {
        return $this->_nodeCount;
    }

    public function exposeNodes(): iterable
    {
        return $this->_nodes;
    }

    public function exposePointCount(): int
    {
        return $this->_pointCount;
    }
    public function exposePointMap()
    {
        return $this->_pointMap;
    }
    public function exposeCircle()
    {
        return $this->_circle;
    }
}
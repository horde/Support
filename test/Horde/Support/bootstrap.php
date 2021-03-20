<?php
if (!class_exists('Horde_Test_Bootstrap')) {
    require_once 'Horde/Test/Bootstrap.php';
}
require_once 'Helper/ConsistentHashInstrumented.php';
Horde_Test_Bootstrap::bootstrap(dirname(__FILE__));

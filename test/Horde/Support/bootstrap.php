<?php
$composerAutoload = __DIR__ . '/../../../vendor/autoload.php';
// Catch the case where we use composer but phpunit is installed externally
if (file_exists($composerAutoload)) {
    require_once $composerAutoload;
}

if (!class_exists(\Horde_Test_Bootstrap::class)) {
    require_once 'Horde/Test/Bootstrap.php';
}

require_once 'Helper/ConsistentHashInstrumented.php';
Horde_Test_Bootstrap::bootstrap(__DIR__);
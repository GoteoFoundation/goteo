<?php

use Symfony\Component\DependencyInjection;
use Symfony\Component\DependencyInjection\Reference;
// Getting default container
$sc = include(__DIR__ . '/../../../src/container.php');

// TODO: add listeners for calls

return $sc;

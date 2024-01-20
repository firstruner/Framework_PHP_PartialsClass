<?php

header("Content-Type: text/plain");

// Only one require for use Framework Loader
require __DIR__ . '/System/Reflection/Dependencies/Loader.php';
require __DIR__ . '/System/Reflection/Dependencies/FluentLoader.php';

// For multiple use of Loader class
use System\Reflection\Dependencies\Loader;

Loader::SetLogActivation(true);
// Load all php standard POO files in "System" folder
Loader::Load(__DIR__ . '/System', php_as_partial: true);

// It's recommended to load interfaces before class
Loader::Load(__DIR__ . '/Samples_Interfaces', php_as_partial: true);

// Load partial samples classes
Loader::Load(__DIR__ . '/test/Class',
      php_as_partial: true,
      ignored:array(__DIR__ . '/test/Class/MultiplesSimple/MultiplesSimple_Methods.php'));

// Consume partial class
echo '--- Use Class ---' . PHP_EOL;

// Consume an instance class object
$obj = new Samples\Class\Simple();
$obj->PrintInstanceMessage();

// Consume a static method in same class object
echo PHP_EOL;
Samples\Class\Simple::PrintStaticMessage();

$final = new Samples\Class\MultiplesSimple();
$final->id = 32;
echo "ID = " . $final->id;
$final->MethodTwo();
<?php

/**
 * This file is a partial class sample
 */

/**
 * Copyright since 2024 Firstruner and Contributors
 * Firstruner is an Registered Trademark & Property of Christophe BOULAS
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Freemium License
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to contact@firstruner.fr so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit, reproduce ou modify this file.
 * Please refer to https://firstruner.fr/ or contact Firstruner for more information.
 *
 * @author    Firstruner and Contributors <contact@firstruner.fr>
 * @copyright Since 2024 Firstruner and Contributors
 * @license   https://wikipedia.org/wiki/Freemium Freemium License
 * @version 1.2.0
 */

header("Content-Type: text/plain");

// Only one require for use Framework Loader
require __DIR__ . '/System/Reflection/Dependencies/Loader.php';

// For multiple use of Loader class
use System\Reflection\Dependencies\Loader;

// Load all php POO files in "System" folder
Loader::Load(__DIR__ . '/System', php_as_partial: true);

// Consume partial class
echo '--- Use Class ---' . PHP_EOL;

// Consume an instance class object
$obj = new System\Samples\Simple();
$obj->PrintInstanceMessage();

// Consume a static method in same class object
echo PHP_EOL;
System\Samples\Simple::PrintStaticMessage();
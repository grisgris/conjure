#!/usr/bin/env php
<?php
/**
 * Bootstrap file for Gris-Gris Conjure in Phar phorm.
 *
 * @package    Gris-Gris.Conjure
 *
 * @copyright  Copyright (C) 2013 Respective authors. All rights reserved.
 * @license    Licensed under the MIT License; see LICENSE.md
 */

namespace Conjure;

use Exception;
use Phar;
use Grisgris\Loader;

Phar::interceptFileFuncs();

// Import the Gris-Gris Skeleton.
require_once 'phar://' . __FILE__ . '/lib/import.php';

// Register the application classes with the loader.
Loader::registerNamespace('Conjure', 'phar://' . __FILE__ . '/src');

try
{
	$application = new Application\Cli;
	$application->execute();
}
catch (Exception $e)
{
	fwrite(STDERR, $e->getMessage() . "\n");
	exit($e->getCode());
}
__HALT_COMPILER();?>
<?php
/**
 * @package     Gris-Gris.Conjure
 * @subpackage  Application
 *
 * @copyright   Copyright (C) 2013 Respective authors. All rights reserved.
 * @license     Licensed under the MIT License; see LICENSE.md
 */
namespace Conjure\Application;

use InvalidArgumentException;
use Grisgris\Application\Cli as ApplicationCli;

/**
 * Command line application class for Gris-Gris Conjure.
 *
 * @package Gris-Gris.Conjure
 * @subpackage Application
 * @since 1.0
 */
class Cli extends ApplicationCli
{
	/**
	 * Run the application routines.
	 *
	 * @return void
	 *
	 * @since 1.0
	 */
	protected function doExecute()
	{
		$namespace = '\\Conjure\\Command\\';

		// There are a few different paths to the help command which override anything else.
		if ($this->input->get('h') || $this->input->get('help') || empty($this->input->args[0]))
		{
			$command = $namespace . 'Help';
		}
		// Look at the first non-switch non-variable argument as the command.
		else
		{
			$command = strtolower(filter_var($this->input->args[0], FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW));
			$command = $namespace . ucfirst($command);
		}

		// Nothing found. Panic.
		if (!class_exists($command) || !is_subclass_of($command, '\\Grisgris\\Controller\\Controller'))
		{
			throw new InvalidArgumentException(sprintf('The `%s` command is not supported.', $command));
		}

		// Get the controller instance based on the command and execute it.
		$controller = new $command($this->provider->createChild());
		$controller->execute();
	}

	/**
	 * Return an empty array as there are is no configuration file for Gris-Gris
	 * Conjure.
	 *
	 * @return array
	 *
	 * @since 1.0
	 */
	protected function fetchConfigurationData()
	{
		return array();
	}
}

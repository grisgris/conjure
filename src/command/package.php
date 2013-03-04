<?php
/**
 * @package     Gris-Gris.Conjure
 * @subpackage  Command
 *
 * @copyright   Copyright (C) 2013 Respective authors. All rights reserved.
 * @license     Licensed under the MIT License; see LICENSE.md
 */

namespace Conjure\Command;

use InvalidArgumentException;
use Grisgris\Controller\Base as Command;
use Conjure\Phar\Phar;
use Conjure\Phar\ImporterGitRepository as RepositoryImporter;
use Conjure\Phar\ImporterGrisgrisSkeleton as SkeletonImporter;

/**
 * Package command class for Gris-Gris Conjure.
 *
 * @package     Gris-Gris.Conjure
 * @subpackage  Command
 * @since       1.0
 */
class Package extends Command
{
	/**
	 * Execute the command.
	 *
	 * @return  boolean  True if controller finished execution, false if the controller did not
	 *                   finish execution. A controller might return false if some precondition for
	 *                   the controller to run has not been satisfied.
	 *
	 * @since   1.0
	 * @throws  LogicException
	 * @throws  RuntimeException
	 */
	public function execute()
	{
		// Check on some basic inputs.
		$quiet = $this->input->getBool('q', false);
		$manifestPath = $this->input->getString('f', $this->application->get('cwd', realpath(getcwd())) . '/conjure.xml');

		$quiet or $this->application->out();
		$quiet or $this->application->out('Conjuring a package.');

		// Get the XML manifest parsed and as an object.
		$quiet or $this->application->out('--------------------------------------------------------------------------------');
		$quiet or $this->application->out('. reading the package manifest.');
		try
		{
			$manifest = $this->_fetchPackageManifest($manifestPath);
		}
		catch (InvalidArgumentException $e)
		{
			$this->application->out('ERROR: ' . $e->getMessage());
			$this->displayUsage();
			$this->close();
		}

		// If there isn't a code section in the manifest we have nothing to do.
		if (!isset($manifest->code[0]))
		{
			$quiet or $this->application->out('. no code section found in the manifest.');
			return;
		}

		// Ensure that we have at maximum one skeleton entry in the manifest.
		if (isset($manifest->code[0]->platform[1]))
		{
			$quiet or $this->application->out('. only one skeleton entry can be in a manifest.');
			return;
		}

		// Create the packager object.
		$quiet or $this->application->out('. creating the package object.');
		$phar = new Phar(
			(string) $manifest['destination'],
			((string) $manifest['minify'] == 'true') ? true : false,
			((string) $manifest['alias'])
		);
		$quiet or $this->application->out('.. created the package object.');

		// Process the items in the code section of the manifest.
		$quiet or $this->application->out('. adding files from the code section.');
		foreach ($manifest->code[0]->children() as $item)
		{
			switch ($item->getName())
			{
				// Import a single file.
				case 'file':
					$quiet or $this->application->out(sprintf('.. importing %s.', (string) $item));
					$phar->addFile(realpath(dirname($manifestPath)) . '/' . (string) $item, (string) $item['localPath']);
					break;

					// Import a folder ... either recursively or not.
				case 'folder':
					// Check to see if we want to import the folder recursively.
					if ((string) $item['recursive'] == 'true')
					{
						$quiet or $this->application->out(sprintf('.. importing %s recursively.', (string) $item));
						$phar->addDirectoryRecursive(realpath(dirname($manifestPath)) . '/' . (string) $item, (string) $item['localPath']);
					}
					else
					{
						$quiet or $this->application->out(sprintf('.. importing %s.', (string) $item));
						$phar->addDirectoryFiles(realpath(dirname($manifestPath)) . '/' . (string) $item, (string) $item['localPath']);
					}
					break;

					// Import the Gris-Gris Skeleton.
				case 'skeleton':
					$quiet or $this->application->out('... creating the skeleton importer.');
					$importer = new SkeletonImporter($phar, (string) $item['localPath']);
					$quiet or $this->application->out('.... created the skeleton importer.');

					$quiet or $this->application->out('... importing the skeleton.');
					$importer->import($item);
					$quiet or $this->application->out('.... imported the skeleton.');
					break;

					// Import the Git Repository.
				case 'git':
					$quiet or $this->application->out('... creating the git repository importer.');
					$importer = new RepositoryImporter($phar, (string) $item['localPath']);
					$quiet or $this->application->out('.... created the git repository importer.');

					$quiet or $this->application->out('... importing the repository.');
					$importer->import($item);
					$quiet or $this->application->out('.... imported the repository.');
					break;

				default:
					throw new InvalidArgumentException(sprintf('Unable to process tag <%s> for packaging.', $item->getName()));
					break;
			}
		}

		$quiet or $this->application->out('. setting the package stub file(s).');
		if ((string) $manifest->code[0]['stub'])
		{
			$phar->setStub(
				realpath(dirname($manifestPath)) . '/' . (string) $manifest->code[0]['stub']
			);
		}
		else
		{
			$phar->setStubs(
				(string) $manifest->code[0]['cli'],
				(string) $manifest->code[0]['web']
			);
		}
		$quiet or $this->application->out('.. set the package stub file(s).');

		// Write the package out to disk.
		$quiet or $this->application->out('. writing the package to disk.');
		$phar->write();
	}

	/**
	 * Get the package manifest object from a filesystem path.
	 *
	 * @param   string  $manifestPath  The absolute filesystem path to the manifest file to parse and return.
	 *
	 * @return  SimpleXMLElement
	 *
	 * @since   1.0
	 * @throws  InvalidArgumentException
	 */
	private function _fetchPackageManifest($manifestPath)
	{
		// Set relative paths to be relative to the current working directory.
		if (strpos($manifestPath, '/') !== 0)
		{
			$manifestPath = $this->application->get('cwd') . '/' . $manifestPath;
		}

		// Ensure a path has been specified.
		if (empty($manifestPath))
		{
			throw new InvalidArgumentException('You must specify a manifest file path or use interactive mode.');
		}

		// Ensure the path exists.
		if (!is_file($manifestPath))
		{
			throw new InvalidArgumentException('The path specified for your manifest file does not exist.');
		}

		// Load the manifest and parse it.
		$manifest = simplexml_load_file(realpath($manifestPath));

		return $manifest;
	}
}

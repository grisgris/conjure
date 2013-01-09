<?php
/**
 * @package     Gris-Gris.Conjure
 * @subpackage  Phar
 *
 * @copyright   Copyright (C) 2013 Respective authors. All rights reserved.
 * @license     Licensed under the MIT License; see LICENSE.md
 */

namespace Conjure\Phar;

use DirectoryIterator;
use InvalidArgumentException;
use SimpleXMLElement;
use Conjure\Git\Repository as GitRepository;

/**
 * Gris-Gris Skeleton importer class for the Phar packager.
 *
 * @package     Gris-Gris.Conjure
 * @subpackage  Phar
 * @since       1.0
 */
class ImporterGrisgrisSkeleton extends Importer
{
	/**
	 * Import the Gris-Gris Skeleton based on the XML element from the manifest.
	 *
	 * @param   SimpleXMLElement  $el  The XML element containing information about how to import the skeleton.
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function import(SimpleXMLElement $el)
	{
		$repoPath = (string) $el['repository'];
		$repoPath = $repoPath ? $repoPath : 'http://github.com/grisgris/skeleton.git';

		$basePath = $this->_fetchGitRepository($repoPath, (string) $el['version']) . '/src';

		// Validate that the Skeleton import file exists.
		if (!is_file($basePath . '/import.php'))
		{
			throw new InvalidArgumentException('The skeleton import file could not be found.');
		}

		// Add the import file.
		$this->importFile($basePath . '/import.php');

		// Get the appropriate packages to import.
		$packages = $this->_fetchPackagesToImport($el, $basePath);

		// If no packages were specified then assume we go for everything.
		if (empty($packages))
		{
			$this->importDirectoryRecursive($basePath);
		}
		else
		{
			// Add just the enumerated packages.
			foreach ($packages as $package)
			{
				$this->importDirectoryRecursive($basePath . '/' . $package, '/' . $package);
			}
		}
	}

	/**
	 * Get the base path for a clone of the Git repository (creating it if necessary).
	 *
	 * @param   string  $url  The URL of the git repository to clone/update.
	 * @param   string  $ref  The ref to use in the git repository.
	 *
	 * @return  string  The base path for the git repository.
	 *
	 * @since   1.0
	 * @throws  RuntimeException
	 */
	private function _fetchGitRepository($url, $ref = 'master')
	{
		// Create a Git repository object within the system tmp folder for the url.
		$root = sys_get_temp_dir() . md5($url);

		// If the folder doesn't exist attempt to create it.
		if (!is_dir($root))
		{
			mkdir($root, 0777, true);
		}

		// Instantiate the repository object.
		$repo = new GitRepository($root);
		if (!$repo->exists())
		{
			$repo->create($url);
		}

		// Get a clean checkout of the branch/tag required.
		$repo->fetch()
			->branchCheckout($ref)
			->clean();

		return $root;
	}

	/**
	 * Get all Gris-Gris Skeleton packages to import based on the XML element.
	 *
	 * @param   SimpleXMLElement  $el        The XML element containing information about how to import the platform.
	 * @param   string            $basePath  The filesystem path to the libraries.
	 *
	 * @return  array  The packages to import.
	 *
	 * @since   1.0
	 */
	private function _fetchPackagesToImport(SimpleXMLElement $el, $basePath)
	{
		$packages = array();

		// If we have no packages specified assume we'll get them all imported.
		if (!isset($el->packages[0]) || !isset($el->packages[0]->package[0]))
		{
			return $packages;
		}

		// Get the package set element and determine if we are using an exclusion rule or not.
		$packageSet = $el->packages[0];
		$exclude = (((string) $packageSet['exclude'] == 'true') ? true : false);

		// Get the enumerated packages from the XML.
		$enumerated = array();
		foreach ($packageSet->package as $p)
		{
			$enumerated[] = (string) $p['name'];
		}

		// We are using an exclusion rule.  Sounds like work.
		if ($exclude)
		{
			// Iterate the main package directory contents.
			$directory = new DirectoryIterator($basePath);
			foreach ($directory as $child)
			{
				if ($child->isDir() && !$child->isDot() && !in_array($child->getFilename(), $enumerated))
				{
					$packages[] = $child->getFilename();
				}
			}
		}
		// Easy peasy, just get the enumerated packages.
		else
		{
			$packages = $enumerated;
		}

		// Make sure we have unique values.
		$packages = array_unique($packages);

		return $packages;
	}
}

<?php
/**
 * @package     Gris-Gris.Conjure
 * @subpackage  Phar
 *
 * @copyright   Copyright (C) 2013 Respective authors. All rights reserved.
 * @license     Licensed under the MIT License; see LICENSE.md
 */

namespace Conjure\Phar;

use InvalidArgumentException;
use SimpleXMLElement;
use Conjure\Git\Repository as GitRepository;

/**
 * Git Repository importer class for the Phar packager.
 *
 * @package     Gris-Gris.Conjure
 * @subpackage  Phar
 * @since       1.0
 */
class ImporterGitRepository extends Importer
{
	/**
	 * Import the Git Repository based on the XML element from the manifest.
	 *
	 * @param   SimpleXMLElement  $el  The XML element containing information about how to import the repository.
	 *
	 * @return  void
	 *
	 * @since   1.0
	 * @throws  InvalidArgumentException
	 */
	public function import(SimpleXMLElement $el)
	{
		$repositoryPath = $this->_fetchGitRepository((string) $el['url'], (string) $el['ref']);

		// Process the items in the code section of the manifest.
		foreach ($el->children() as $item)
		{
			switch ($item->getName())
			{
				// Import a single file.
				case 'file':
					$this->_quiet or $this->out(sprintf('... importing %s.', (string) $item));
					$this->importFile($repositoryPath . '/' . (string) $item, (string) $item['localPath']);
					break;

				// Import a folder ... either recursively or not.
				case 'folder':
					// Check to see if we want to import the folder recursively.
					if ((string) $item['recursive'] == 'true')
					{
						$this->_quiet or $this->out(sprintf('... importing %s recursively.', (string) $item));
						$this->importDirectoryRecursive($repositoryPath . '/' . (string) $item, (string) $item['localPath']);
					}
					else
					{
						$this->_quiet or $this->out(sprintf('... importing %s.', (string) $item));
						$this->importDirectoryFiles($repositoryPath . '/' . (string) $item, (string) $item['localPath']);
					}
					break;

				default:
					throw new InvalidArgumentException(sprintf('Unable to process tag <%s> with the Git repository importer.', $item->getName()));
					break;
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
			$repo->create();
		}

		// Get a clean checkout of the branch/tag required.
		$repo->fetch()
			->branchCheckout($ref)
			->clean();

		return $root;
	}
}

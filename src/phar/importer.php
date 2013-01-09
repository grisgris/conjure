<?php
/**
 * @package     Gris-Gris.Conjure
 * @subpackage  Phar
 *
 * @copyright   Copyright (C) 2013 Respective authors. All rights reserved.
 * @license     Licensed under the MIT License; see LICENSE.md
 */

namespace Conjure\Phar;

use SimpleXMLElement;
use Conjure\Phar\Phar;

/**
 * Abstract file importer class for the Phar packager.
 *
 * @package     Gris-Gris.Conjure
 * @subpackage  Phar
 * @since       1.0
 */
abstract class Importer
{
	/**
	 * @var    Phar  The Phar package object in which to import the files.
	 * @since  1.0
	 */
	private $_packager;

	/**
	 * @var    string  The local path within the Phar package to import files.
	 * @since  1.0
	 */
	private $_pharPath;

	/**
	 * Object Constructor.
	 *
	 * @param   Phar    $packager  The Phar package object in which to import the files.
	 * @param   string  $pharPath  The local path within the Phar package to import files.
	 *
	 * @since   1.0
	 */
	public function __construct(Phar $packager, $pharPath = null)
	{
		$this->_packager = $packager;
		$this->_pharPath = $pharPath;
	}

	/**
	 * Import files based on the XML element from the manifest.
	 *
	 * @param   SimpleXMLElement  $el  The XML element containing information about how to import the files.
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	abstract public function import(SimpleXMLElement $el);

	/**
	 * Import a directory's files into the Phar package; without recursing into children.
	 *
	 * @param   string  $path              The absolute filesystem path to the directory to import.
	 * @param   string  $pharPathExtended  The [optional] local path to append to the importer's local path.
	 *
	 * @return  void
	 *
	 * @since   1.0
	 * @throws  InvalidArgumentException
	 */
	protected function importDirectoryFiles($path, $pharPathExtended = null)
	{
		$this->_packager->addDirectoryFiles($path, $this->_pharPath . $pharPathExtended);
	}

	/**
	 * Import a directory's files into the Phar package; recursing into children.
	 *
	 * @param   string  $path              The absolute filesystem path to the directory to import.
	 * @param   string  $pharPathExtended  The [optional] local path to append to the importer's local path.
	 *
	 * @return  void
	 *
	 * @since   1.0
	 * @throws  InvalidArgumentException
	 */
	protected function importDirectoryRecursive($path, $pharPathExtended = null)
	{
		$this->_packager->addDirectoryRecursive($path, $this->_pharPath . $pharPathExtended);
	}

	/**
	 * Import a file into the Phar package.
	 *
	 * @param   string  $path              The absolute filesystem path to the file to import.
	 * @param   string  $pharPathExtended  The [optional] local path to append to the importer's local path.
	 *
	 * @return  void
	 *
	 * @since   1.0
	 * @throws  InvalidArgumentException
	 */
	protected function importFile($path, $pharPathExtended = null)
	{
		$this->_packager->addFile($path, $this->_pharPath . $pharPathExtended);
	}
}

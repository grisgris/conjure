<?php
/**
 * @package     Gris-Gris.Conjure
 * @subpackage  Git
 *
 * @copyright   Copyright (C) 2013 Respective authors. All rights reserved.
 * @license     Licensed under the MIT License; see LICENSE.md
 */

namespace Conjure\Git;

use InvalidArgumentException;
use RuntimeException;

/**
 * Class to manage a git repository.
 *
 * @package     Gris-Gris.Conjure
 * @subpackage  Git
 * @since       1.0
 */
class Repository
{
	/**
	 * @var    string  The filesystem path for the repository root.
	 * @since  1.0
	 */
	private $_root;

	/**
	 * Object Constructor.
	 *
	 * @param   string  $root  The filesystem path for the repository root.
	 *
	 * @since   1.0
	 */
	public function __construct($root)
	{
		$this->_root = $root;
	}

	/**
	 * Check if the repository exists.
	 *
	 * @return  boolean  True if the repository exists.
	 *
	 * @since   1.0
	 */
	public function exists()
	{
		// If we don't have a configuration file for the repository it doesn't exist.
		return file_exists($this->_root . '/.git/config');
	}

	/**
	 * Clone a repository from a given remote.
	 *
	 * @param   string  $remote  The URI from which to clone the repository.
	 *
	 * @return  Repository  This repository object for chaining.
	 *
	 * @since   1.0
	 * @throws  InvalidArgumentException
	 * @throws  RuntimeException
	 */
	public function create($remote)
	{
		// Initialize variables.
		$out = array();
		$return = null;

		if (!file_exists($this->_root . '/.git'))
		{
			exec('git clone -q ' . escapeshellarg($remote) . ' ' . escapeshellarg($this->_root), $out, $return);
		}
		else
		{
			throw new InvalidArgumentException('Repository already exists at ' . $this->_root . '.');
		}

		if ($return !== 0)
		{
			throw new RuntimeException(sprintf('The clone failed from remote %s with code %d and message %s.', $remote, $return, implode("\n", $out)));
		}

		return $this;
	}

	/**
	 * Fetch updates from a repository remote.
	 *
	 * @param   string  $remote  The remote name from which to fetch changes.
	 *
	 * @return  Repository  This repository object for chaining.
	 *
	 * @since   1.0
	 * @throws  InvalidArgumentException
	 * @throws  RuntimeException
	 */
	public function fetch($remote = 'origin')
	{
		$out = array();
		$return = null;

		// Ensure that either the remote exists or is a valid URL.
		if (!filter_var($remote, FILTER_VALIDATE_URL) && !in_array($remote, $this->_getRemotes()))
		{
			throw new InvalidArgumentException('No valid remote ' . $remote . ' exists.');
		}

		$wd = getcwd();
		chdir($this->_root);
		exec('git fetch -q ' . escapeshellarg($remote), $out, $return);
		chdir($wd);

		if ($return !== 0)
		{
			throw new RuntimeException(sprintf('The fetch failed from remote %s with code %d and message %s.', $remote, $return, implode("\n", $out)));
		}

		return $this;
	}

	/**
	 * Merge a branch by name.
	 *
	 * @param   string  $branch  The name of the branch to merge.
	 *
	 * @return  Repository  This repository object for chaining.
	 *
	 * @since   1.0
	 * @throws  RuntimeException
	 */
	public function merge($branch = 'origin/master')
	{
		$out = array();
		$return = null;

		$wd = getcwd();
		chdir($this->_root);
		exec('git merge ' . escapeshellarg($branch), $out, $return);
		chdir($wd);

		if ($return !== 0)
		{
			throw new RuntimeException(sprintf('Unable to merge branch %s with code %d and message %s.', $branch, $return, implode("\n", $out)));
		}

		return $this;
	}

	/**
	 * Add a remote to the repository.
	 *
	 * @param   string  $name  The name of the remote to add.
	 * @param   string  $url   The URI of the remote to add.
	 *
	 * @return  Repository  This repository object for chaining.
	 *
	 * @since   1.0
	 * @throws  InvalidArgumentException
	 * @throws  RuntimeException
	 */
	public function remoteAdd($name, $url)
	{
		$out = array();
		$return = null;

		if (in_array($name, $this->_getRemotes()))
		{
			throw new InvalidArgumentException('The remote ' . $name . ' already exists.');
		}

		$wd = getcwd();
		chdir($this->_root);
		exec('git remote add ' . escapeshellarg($name) . ' ' . escapeshellarg($url), $out, $return);
		chdir($wd);

		if ($return !== 0)
		{
			throw new RuntimeException(
				sprintf('The remote %s could not be added from %s with code %d and message %s.', $name, $url, $return, implode("\n", $out))
			);
		}

		return $this;
	}

	/**
	 * Check if a remote exists for the repository by name.
	 *
	 * @param   string  $name  The remote name to check.
	 *
	 * @return  boolean  True if the remote exists.
	 *
	 * @since   1.0
	 */
	public function remoteExists($name)
	{
		return in_array($name, $this->_getRemotes());
	}

	/**
	 * Set the remote URL for the repository by name.
	 *
	 * @param   string  $name  The name of the remote to change.
	 * @param   string  $url   The URI of the remote to set.
	 *
	 * @return  Repository  This repository object for chaining.
	 *
	 * @since   1.0
	 * @throws  InvalidArgumentException
	 * @throws  RuntimeException
	 */
	public function remoteSetUrl($name, $url)
	{
		$out = array();
		$return = null;

		if (!in_array($name, $this->_getRemotes()))
		{
			throw new InvalidArgumentException('The remote ' . $name . ' doesn\'t exist.  Try adding it.');
		}

		$wd = getcwd();
		chdir($this->_root);
		exec('git remote set-url ' . escapeshellarg($name) . ' ' . escapeshellarg($url), $out, $return);
		chdir($wd);

		if ($return !== 0)
		{
			throw new RuntimeException(
				sprintf('Could not set the url %s for remote %s. Error code %d and message %s.', $url, $name, $return, implode("\n", $out))
			);
		}

		return $this;
	}

	/**
	 * Remove a remote from the repository by name.
	 *
	 * @param   string  $name  The remote name to remove.
	 *
	 * @return  Repository  This repository object for chaining.
	 *
	 * @since   1.0
	 * @throws  RuntimeException
	 */
	public function remoteRemove($name)
	{
		$out = array();
		$return = null;

		if (!in_array($name, $this->_getRemotes()))
		{
			return $this;
		}

		$wd = getcwd();
		chdir($this->_root);
		exec('git remote rm ' . escapeshellarg($name), $out, $return);
		chdir($wd);

		if ($return !== 0)
		{
			throw new RuntimeException(sprintf('The remote %s could not be removed with code %d and message %s.', $name, $return, implode("\n", $out)));
		}

		return $this;
	}

	/**
	 * Check out a branch by name.
	 *
	 * @param   string  $name  The branch name to checkout.
	 *
	 * @return  Repository  This repository object for chaining.
	 *
	 * @since   1.0
	 * @throws  RuntimeException
	 */
	public function branchCheckout($name)
	{
		$out = array();
		$return = null;

		$wd = getcwd();
		chdir($this->_root);
		exec('git checkout -q ' . escapeshellarg($name), $out, $return);
		chdir($wd);

		if ($return !== 0)
		{
			throw new RuntimeException(sprintf('Branch %s could not be checked out with code %d and message %s.', $name, $return, implode("\n", $out)));
		}

		return $this;
	}

	/**
	 * Create a branch on the repository.
	 *
	 * @param   string  $name          The name for the new branch to create.
	 * @param   string  $parent        The name of the branch from which we are creating.
	 * @param   string  $parentRemote  The name of the remote from which we are creating [optional for a local branch].
	 *
	 * @return  Repository  This repository object for chaining.
	 *
	 * @since   1.0
	 * @throws  InvalidArgumentException
	 * @throws  RuntimeException
	 */
	public function branchCreate($name, $parent = 'master', $parentRemote = null)
	{
		$out = array();
		$return = null;

		if (in_array($name, $this->_getBranches()))
		{
			throw new InvalidArgumentException('The branch ' . $name . ' already exists.');
		}

		// If we have a parent remote then fetch latest updates and set up the parent.
		if (!empty($parentRemote))
		{
			$this->fetch($parentRemote);

			$parent = $parentRemote . '/' . $parent;
		}

		$wd = getcwd();
		chdir($this->_root);
		exec('git checkout -b ' . escapeshellarg($name) . ' ' . escapeshellarg($parent), $out, $return);
		chdir($wd);

		if ($return !== 0)
		{
			throw new RuntimeException(sprintf('Branch %s could not be created with code %d and message %s.', $name, $return, implode("\n", $out)));
		}

		return $this;
	}

	/**
	 * Check if a local branch exists for the repository by name.
	 *
	 * @param   string  $name  The branch name to check.
	 *
	 * @return  boolean  True if the remote exists.
	 *
	 * @since   1.0
	 */
	public function branchExists($name = 'joomla')
	{
		return in_array($name, $this->_getBranches());
	}

	/**
	 * Remove a branch from the repository.
	 *
	 * @param   string  $name  The branch name to remove.
	 *
	 * @return  Repository  This repository object for chaining.
	 *
	 * @since   1.0
	 * @throws  RuntimeException
	 */
	public function branchRemove($name = 'staging')
	{
		$out = array();
		$return = null;

		if (!in_array($name, $this->_getBranches()))
		{
			return $this;
		}

		$wd = getcwd();
		chdir($this->_root);
		exec('git branch -D ' . escapeshellarg($name), $out, $return);
		chdir($wd);

		if ($return !== 0)
		{
			throw new RuntimeException(sprintf('Branch %s could not be removed with code %d and message %s.', $name, $return, implode("\n", $out)));
		}

		return $this;
	}

	/**
	 * Clean the repository of untracked files and folders.
	 *
	 * @return  Repository  This repository object for chaining.
	 *
	 * @since   1.0
	 * @throws  RuntimeException
	 */
	public function clean()
	{
		$out = array();
		$return = null;

		$wd = getcwd();
		chdir($this->_root);
		exec('git clean -fd', $out, $return);
		chdir($wd);

		if ($return !== 0)
		{
			throw new RuntimeException(sprintf('Failure cleaning the repository with code %d and message %s.', $return, implode("\n", $out)));
		}

		return $this;
	}

	/**
	 * Reset the current repository branch.
	 *
	 * @param   boolean  $hard  True to perform a hard reset.
	 *
	 * @return  Repository  This repository object for chaining.
	 *
	 * @since   1.0
	 * @throws  RuntimeException
	 */
	public function reset($hard = true)
	{
		$out = array();
		$return = null;

		$flag = $hard ? ' --hard' : '';

		$wd = getcwd();
		chdir($this->_root);
		exec('git reset' . $flag, $out, $return);
		chdir($wd);

		if ($return !== 0)
		{
			throw new RuntimeException(sprintf('Failure resetting the repository with code %d and message %s.', $return, implode("\n", $out)));
		}

		return $this;
	}

	/**
	 * Get a list of the repository local branch names.
	 *
	 * @return  array
	 *
	 * @since   1.0
	 * @throws  RuntimeException
	 */
	private function _getBranches()
	{
		// If we don't have a configuration file for the repository PANIC!
		if (!file_exists($this->_root . '/.git/config'))
		{
			throw new RuntimeException('Not a valid Git repository at ' . $this->_root);
		}

		$branches = array();

		// Go find the remotes from the configuration file.
		$config = parse_ini_file($this->_root . '/.git/config', true);
		foreach ($config as $section => $data)
		{
			if (strpos($section, 'branch ') === 0)
			{
				$branches[] = trim(substr($section, 7));
			}
		}

		return $branches;
	}

	/**
	 * Get a list of the repository remote names.
	 *
	 * @return  array
	 *
	 * @since   1.0
	 * @throws  RuntimeException
	 */
	private function _getRemotes()
	{
		// If we don't have a configuration file for the repository PANIC!
		if (!file_exists($this->_root . '/.git/config'))
		{
			throw new RuntimeException('Not a valid Git repository at ' . $this->_root);
		}

		$remotes = array();

		// Go find the remotes from the configuration file.
		$config = parse_ini_file($this->_root . '/.git/config', true);
		foreach ($config as $section => $data)
		{
			if (strpos($section, 'remote ') === 0)
			{
				$remotes[] = trim(substr($section, 7));
			}
		}

		return $remotes;
	}
}

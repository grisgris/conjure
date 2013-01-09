<?php
/**
 * @package     Gris-Gris.Conjure
 * @subpackage  Command
 *
 * @copyright   Copyright (C) 2013 Respective authors. All rights reserved.
 * @license     Licensed under the MIT License; see LICENSE.md
 */

namespace Conjure\Command;

use Grisgris\Controller\Base as Command;

/**
 * Help command class for Gris-Gris Conjure.
 *
 * @package     Gris-Gris.Conjure
 * @subpackage  Command
 * @since       1.0
 */
class Help extends Command
{
	/**
	 * Execute the command.
	 *
	 * @return  boolean  True if command finished execution, false if the command did not
	 *                   finish execution. A command might return false if some precondition for
	 *                   the command to run has not been satisfied.
	 *
	 * @since   1.0
	 */
	public function execute()
	{
		$man = <<<'OUT'

CONJURE(1)                    Gris-Gris Conjurer                    CONJURE(1)

NAME
     conjure -- create PHP magic

SYNOPSIS
     conjure [command] [options] [files | directories]

DESCRIPTION
     @TODO

OPTIONS
     Unless specifically stated otherwise, options are applicable in all oper-
     ating modes.

     -f manifest-file
             Specify the manifest file to use.

ENVIRONMENT
     The following environment variables affect the execution of conjure:

     LANG       The locale to use.  See environ(7) for more information.

     TZ         The timezone to use when displaying dates.  See environ(7) for
                more information.

EXIT STATUS
     Conjure exits 0 on success, and >0 if an error occurs.

EXAMPLES
     @TODO

GRIS-GRIS                        Jan 1, 2013                         GRIS-GRIS
OUT;

		$this->application->out($man);

		return true;
	}
}

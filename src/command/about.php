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
 * About command class for Gris-Gris Conjure.
 *
 * @package     Gris-Gris.Conjure
 * @subpackage  Command
 * @since       1.0
 */
class About extends Command
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
		$man = <<<'MARK'
                      ~NMMMMMMMMMMMMMMMMMMMM8?.
                    OMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMN
                   OMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMM7
                  DMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMM
                  MMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMM+
                 DMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMM
                ,MMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMM7
                NMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMM
                MMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMM
                MMMMMMMMMMMNZONMMMMMMMMMMMMMMMMMMMMMMMMMMMMM,
                MMMMMMMN.        .MMMMMMMMMMMMMMMMMMMMMMMMMM:
               ,MMMMMN             .MMMMMMMMMMMMMMMMMMMMMMMM=
               ?MMMM7               .DMMMMMMMM,   ..DMMMMMMM=
               7MMMM                  MMMMMN          IMMMMM~
               7MMM$                  DMMMI            .MMMM:
               7MMM.                  :MMM              $MMM,
               ?MMM                    MM               .NMM.
               :MMM                   .M8                ~MM
                MMM:                  +M?                 MM
                MMMM                  MM?                 MM
                MMMM.                ~MM8                :MM
                $MMMM7              OMMMM               .NMM
                .MMMMMM.          ,MMMMMMM              ?MMN
                 .MMMMMMMM~. ..=MMMMMMMMMM+             MMM7
                   MMMMMMMMMMMMMMMMMMMMMMMMM.         NMMMM:
                    MMMMMMMMMMMMMMMMMMMMMMMMMMZ,.. ?MMMMMMM
                     OMMMMMMMMMMMMMMMM+ MMMMMMMMMMMMMMMMMM.
                      8MMMMMMMMMMMMMMM  MMMMMMMMMMMMMMMMM.
                       .MMMMMMMMMMMMM.  7MMMMMMMMMMMMMMM
                        ZMMMMMMMMMMMM    MMMMMMMMMMMMMM,
                         .MMMMMMMMMM8 7M .MMMMMMMMMMMM
                          ,MMMMMMMMMMMMMMM8MMMMMMMMM7
                           .MMMMMMMMMMMMMMMMMMMMMMM$
                            MMMMMMMMMMMMMMMMMMMMMMD.
                           ZMMMMOMMMMMMMMMMMMMMMMMM=
                          IMMMMM7MMMMMMM$MMMMMMMMMMMZ
                         .MMMMMIMMMMMMMM=MMMMMMZMMMMM.
                         MMMMMM.MMMMMMMM+MMMMMM.MMMMMM
                        ?MMMMMM.MMMMMMMM=MMMMMM.MMMMMM,
                         .$DD8I:MMMMMMMMDNMMMMM:MMMMMMN.
                                  . ,~.   .,MMD..OMMN7



                    ?MMM                                         ZMMM
                    :MMM                                         ?MMM

.7MMN.MMM~ $88..O8~ :888  .?MMMMN+            MMM8ZMMM  88$ :8O, +88+  .DMMMMN.
MMMMNMMMM~ MMMMMMMMDZMMM  MMM= $MMD         ,MMMMMMMMM  MMMMMMMM.NMMN MMMM  MMM8
MMM+ .MMM~ MMM+ NMMM$MMM :MMM   MMM         ZMMM  MMMM  MMM, MMM DMM8 MMMM  MMMM
MMM= .MMM, MMM~ 8MMM7MMM :MMM   MMM.......  7MMM  DMMM  MMM. MMM 8MMZ MMMM  MMMM
MMM~ .MMM, MMM:     ?MMM .MMMMO:    NMMMMMM.?MMM  8MMM  MMM      ZMMZ :MMMMZ
MMM, .MMM. NMM,     ~MMM     ,MMMM.  ...... =MMM  8MMM  MMM.     IMM$    .$MMMM.
MMM,  MMM  DMM.     ,MMM .MMM  .MM7         :MMM  ZMMM  MMM      +MMI NMMM  MMM=
MMMMMMMMM  OMM      .MMM .MMMM7MMM,         .MMMMMMMMM  MMM      ~MM+ ~MMM$7MMM
  ZO? MMM  +MM.     .MMN    IZOZ:             =OZ=~MMM  MMD      .MM,   ,7OOI.
     .MMM                                         ?MMM
 .MMMMMM~                                     +MMMMMM.
 .OMMM?.                                      ,DMM8=
MARK;

		$this->application->out($man);

		return true;
	}
}

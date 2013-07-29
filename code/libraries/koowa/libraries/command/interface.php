<?php
/**
 * @package		Koowa_Command
 * @copyright	Copyright (C) 2007 - 2012 Johan Janssens. All rights reserved.
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link     	http://www.nooku.org
 */

/**
 * Command Interface
 *
 * @author		Johan Janssens <johan@nooku.org>
 * @package     Koowa_Command
 */
interface KCommandInterface extends KObjectHandlable
{
    /**
     * Command handler
     *
     * @param   string          $name     The command name
     * @param   KCommandContext $context  The command context
     * @return  boolean
     */
	public function execute( $name, KCommandContext $context);

	/**
	 * Get the priority of the command
	 *
	 * @return	integer The command priority
	 */
  	public function getPriority();
}

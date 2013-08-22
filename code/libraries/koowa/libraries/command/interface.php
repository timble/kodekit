<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright	Copyright (C) 2007 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/koowa for the canonical source repository
 */

/**
 * Command Interface
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Koowa\Library\Command
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

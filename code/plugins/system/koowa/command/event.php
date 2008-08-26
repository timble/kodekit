<?php
/**
 * @version		$Id:proxy.php 46 2008-03-01 18:39:32Z mjaz $
 * @package		Koowa_Command
 * @copyright	Copyright (C) 2007 - 2008 Joomlatools. All rights reserved.
 * @license		GNU GPLv2 <http://www.gnu.org/licenses/old-licenses/gpl-2.0.html>
 * @link     	http://www.koowa.org
 */

/**
 * Event Command
 *
 * @author		Johan Janssens <johan@joomlatools.org>
 * @package     Koowa_Command
 * @uses 		KPatternCommandHandler
 */
class KCommandEvent extends KPatternCommandHandler
{
	/**
	 * Command handler
	 * 
	 * @param string  $name		The command name
	 * @param object  $args		The command arguments
	 *
	 * @return boolean
	 */
	public function execute( $name, $args ) 
	{
		$dispatcher = KFactory::get('lib.koowa.event.dispatcher');
		return $dispatcher->trigger($name, (array) $args);
	}
}

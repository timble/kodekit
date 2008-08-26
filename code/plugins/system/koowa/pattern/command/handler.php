<?php
/**
 * @version		$Id:proxy.php 46 2008-03-01 18:39:32Z mjaz $
 * @package		Koowa_Pattern
 * @subpackage	Command
 * @copyright	Copyright (C) 2007 - 2008 Joomlatools. All rights reserved.
 * @license		GNU GPLv2 <http://www.gnu.org/licenses/old-licenses/gpl-2.0.html>
 * @link     	http://www.koowa.org
 */

/**
 * Command interface
 *
 * @author		Johan Janssens <johan@joomlatools.org>
 * @package     Koowa_Pattern
 * @subpackage  Command
 */
abstract class KPatternCommandHandler extends KObject
{
	/**
	 * Generic Command handler
	 * 
	 * @param string  $name		The command name
	 * @param object  $args		The command arguments
	 *
	 * @return	boolean
	 */
	abstract public function execute( $name, $args);
}

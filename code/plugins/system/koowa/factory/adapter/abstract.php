<?php
/**
 * @version 	$Id:factory.php 46 2008-03-01 18:39:32Z mjaz $
 * @package		Koowa_Factory
 * @copyright	Copyright (C) 2007 - 2008 Joomlatools. All rights reserved.
 * @license		GNU GPLv2 <http://www.gnu.org/licenses/old-licenses/gpl-2.0.html>
 */

/**
 * KFactoryAdpater for the Joomla! framework
 *
 * @author		Johan Janssens <johan@joomlatools.org>
 * @package     Koowa_Factory
 */
abstract class KFactoryAdapterAbstract extends KObject implements KPatternCommandInterface, KFactoryAdapterInterface
{
	/**
	 * Generic Command handler
	 * 
	 * @param string  $name		The command name
	 * @param mixed   $args		The command arguments
	 *
	 * @return object
	 */
	final public function execute($name, $args) 
	{	
		$instance = $this->createInstance($name, $args);
		return $instance;
	}
}
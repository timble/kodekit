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
	 * @param object  $args		The command arguments
	 *
	 * @return string|object|false
	 */
	final public function execute($name, $args) 
	{
		//Create the handle based on the class identifier
		$handle = $this->createHandle($name);
			
		//Return if no handle could be created
		if($handle === false) {
		 	return false;
		}
		
		//Create the instance based on the instance handle
		if(KFactory::has($handle) === false) 
		{
			$instance = $this->createInstance($handle, $args);
			KFactory::set($handle, $instance);
		}
		
		return $handle;
	}
}
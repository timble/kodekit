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
abstract class KFactoryAdapterAbstract extends KPatternCommandHandler
{
	/**
	 * Generic Command handler
	 * 
	 * @param string  $name		The command name
	 * @param object  $args		The command arguments
	 *
	 * @return	mixed
	 */
	final public function execute($name, $args) 
	{
		$instance = $this->getInstance($name, $args);
		if($instance != false) {
			KFactory::set($name, $instance);
			return false;
		}
		
		return true;
	}
	
	/**
	 * Get an instance of a class based on a class identifier
	 *
	 * @param mixed  $string 	The class identifier
	 * @param array  $options 	An optional associative array of configuration settings.
	 *
	 * @return object
	 */
	abstract public function getInstance($identifier, $options = array());
}
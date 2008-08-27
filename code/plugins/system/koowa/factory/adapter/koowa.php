<?php
/**
 * @version 	$Id:factory.php 46 2008-03-01 18:39:32Z mjaz $
 * @package		Koowa_Factory
 * @copyright	Copyright (C) 2007 - 2008 Joomlatools. All rights reserved.
 * @license		GNU GPLv2 <http://www.gnu.org/licenses/old-licenses/gpl-2.0.html>
 */

/**
 * KFactoryAdpater for the Koowa framework
 *
 * @author		Johan Janssens <johan@joomlatools.org>
 * @package     Koowa_Factory
 * @uses 		KInflector
 */
class KFactoryAdapterKoowa extends KFactoryAdapterAbstract
{
	/**
	 * Get an instance of a class based on a class identifier
	 *
	 * @param mixed  $string 	The class identifier
	 * @param array  $options 	An optional associative array of configuration settings.
	 *
	 * @return object
	 */
	public function getInstance($identifier, $options = array())
	{
		$parts = explode('.', $identifier);
		if($parts[0] != 'lib' || $parts[1] != 'koowa') {
			return false;
		}
		
		unset($parts[0]);
		unset($parts[1]);
		
		$classname = 'K'.KInflector::implode($parts);
		
		if (!class_exists($classname))
		{
			$suffix = array_pop($parts);
			$options['name'] = array(
                        'prefix'    => 'k',
						'base'      => KInflector::implode($parts),
						'suffix'    => $suffix                       
                        );
                        
			$classname = 'K'.KInflector::implode($parts).'Default';
		}
		
		
		$instance = new $classname($options);
		return $instance;
	}
}
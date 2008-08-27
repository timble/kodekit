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
interface KFactoryAdapterInterface
{
	/**
	 * Create a object handle based on a class identifier
	 *
	 * @param mixed  $string 	The class identifier
	 *
	 * @return string|false
	 */
	public function createHandle($identifier);

	/**
	 * Create an object instance based on a class identifier
	 *
	 * @param mixed  $string 	The class identifier
	 * @param array  $options 	An optional associative array of configuration settings.
	 *
	 * @return object
	 */
	public function createInstance($identifier, $options);
}
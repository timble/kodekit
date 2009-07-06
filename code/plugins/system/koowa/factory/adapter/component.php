<?php
/**
 * @version 	$Id$
 * @category	Koowa
 * @package		Koowa_Factory
 * @subpackage 	Adapter
 * @copyright	Copyright (C) 2007 - 2009 Johan Janssens and Mathias Verraes. All rights reserved.
 * @license		GNU GPLv2 <http://www.gnu.org/licenses/old-licenses/gpl-2.0.html>
 */

/**
 * Factory adpater for a component
 *
 * @author		Johan Janssens <johan@koowa.org>
 * @category	Koowa
 * @package     Koowa_Factory
 * @subpackage 	Adapter
 */
class KFactoryAdapterComponent extends KFactoryAdapterAbstract
{
	/**
	 * Create an instance of a class based on a class identifier
	 *
	 * @param mixed  The class identifier
	 * @param array  An optional associative array of configuration settings.
	 * @return object|false  Return object on success, returns FALSE on failure
	 */
	public function instantiate($identifier, array $options)
	{
		$instance = false;
		$identifier = new KFactoryIdentifierComponent($identifier);
		if($identifier->extension == 'com') {
			$instance = self::_createInstance($identifier, $options);
		}

		return $instance;
	}

	/**
	 * Get an instance of an instanciatable class
	 *
	 * @param 	KFactoryIdentifierComponent	Identifier
	 * @param 	array	Object options
	 * @throws	KFactoryAdapterException
	 * @return object
	 */
	protected static function _createInstance(KFactoryIdentifierInterface $identifier, array $options = array())
	{
		$instance = false;

        $classname = $identifier->getClassName();

      	if (!class_exists( $classname ))
		{
			//Create path
			if(!isset($options['base_path'])) {
				$options['base_path'] = $identifier->getBasePath();
			}

			//Find the file
			$file = $options['base_path'].DS.$identifier->getFileName();
			if(file_exists($file))
			{
				include $file;
				if (!class_exists( $classname )) {
					throw new KFactoryAdapterException("Class [$classname] not found in file [$file]" );
				}
			}
			else {
				$classname = $identifier->getDefaultClass();
			}
		}

		if(class_exists( $classname ))
		{
			$options['identifier'] = $identifier;
			$instance = new $classname($options);
		}

		return $instance;
	}


}
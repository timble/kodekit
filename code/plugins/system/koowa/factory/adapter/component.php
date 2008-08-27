<?php
/**
 * @version 	$Id:factory.php 46 2008-03-01 18:39:32Z mjaz $
 * @package		Koowa_Factory
 * @copyright	Copyright (C) 2007 - 2008 Joomlatools. All rights reserved.
 * @license		GNU GPLv2 <http://www.gnu.org/licenses/old-licenses/gpl-2.0.html>
 */

/**
 * KFactoryAdpater for a component
 *
 * @author		Johan Janssens <johan@joomlatools.org>
 * @package     Koowa_Factory
 */
class KFactoryAdapterComponent extends KFactoryAdapterAbstract
{
	/**
	 * The alias object map
	 *
	 * @var	array
	 */
	protected static $_objectMap = array(
      	'table'     => 'DatabaseTable',
        'row'       => 'DatabaseRow',
      	'rowset'    => 'DatabaseRowset'
	);
	
	/**
	 * Parse a class identifier to determine if it can be processed
	 *
	 * @param mixed  $string 	The class identifier
	 * @return string|false
	 */
	public function createHandle($identifier)
	{
		$parts = explode('.', $identifier);
		if(strpos($parts[0], 'com') === false) {
			return false;
		}
	
		//Parse the client from the object string
		if(strpos($parts[0], '::') === false)
		{
			$name = KFactory::get('lib.joomla.application')->getName();
			$identifier = $name.'::'.$identifier;
		} 
		else $identifier = str_replace('admin::', 'administrator::', $identifier);
		
		return $identifier;
	}

	/**
	 * Create an instance of a class based on a class identifier
	 *
	 * @param mixed  $string 	The class identifier
	 * @param array  $options 	An optional associative array of configuration settings.
	 * @return object
	 */
	public function createInstance($identifier, $options)
	{
		$parts = explode('.', $identifier);
		
		//Set the application
		$name = explode('::', $parts[0]);
		$result['application'] = $name[0];
		
		//Set the component
		if(isset($parts[1])) {
			$result['component'] = $parts[1];
		} 

		//Set the object type
		if(isset($parts[2])) {
			$result['type'] = $parts[2];
		}

		//Set the object name
		if(isset($parts[3])) {
			$result['name'] = $parts[3];
		}
		
		return self::_getInstanceByArray($result, $options);
	}

	/**
	 * Get an instance of an instanciatable class
	 *
	 * @param 	array	Object information
	 * @param 	array	Object options
	 * @throws	KFactoryAdapterException
	 * @return object
	 */
	protected static function _getInstanceByArray($object, $options = array())
	{
		if(array_key_exists('application', $object)) {
			$client = $object['application'];
		} else {
			$client = KFactory::get('lib.joomla.application')->getName();
		}

		if(array_key_exists('component', $object)) {
			$component = $object['component'];
		} else {
			$component = '';
		}

		if(array_key_exists('type', $object)) {
			$type =  $object['type'];
		} else {
			$type = '';
		}

		if(array_key_exists('name', $object)) {
			$name = $object['name'];
		} else {
			$name = '';
		}

		if(array_key_exists($object['type'], self::$_objectMap)) {
			$base =  self::$_objectMap[$object['type']];
		} else {
			$base = $object['type'];
		}

        $classname = ucfirst($component).ucfirst($type).ucfirst($name);
		
		if (!class_exists( $classname ))
		{
			//Create path
			if(!isset($options['base_path']))
			{
				$options['base_path']  = JApplicationHelper::getClientInfo($client, true)->path;
				$options['base_path'] .= DS.'components'.DS.'com_'.$component;

				if(!empty($name)) {
					$options['base_path'] .= DS.KInflector::pluralize($type);
				}
			}

			//Find the file
			Koowa::import('lib.joomla.filesystem.path');
			if($file = JPath::find($options['base_path'], self::_getFileName($type, $name)))
			{
				require_once $file;
				if (!class_exists( $classname )) {
					throw new KFactoryAdapterException($classname.' not found in file.' );
				}

				//Set the view base_path in the options array
				$options['base_path'] = dirname($file);
			}
			else 
			{
				if(class_exists( 'K'.ucfirst($base).ucfirst($name))) {
					$classname = 'K'.ucfirst($base).ucfirst($name);
				} else {
					$classname = 'K'.ucfirst($base).'Default';
				}	
			}
		}

		//Set the name in the options array
		$options['name'] = array('prefix' => $component, 'base' => $base, 'suffix' => $name);
			
		// Create the object
		$instance = new $classname($options);
		return $instance;
	}

	/**
	 * Get the filename for a specific class
	 *
	 * Function checks to see if the class has a static getFileName function,
	 * otherwise it returns a default name.
	 *
	 * @return string The file name for the class
	 */
	protected static function _getFileName($class, $name)
	{
		$filename = '';
	
		switch($class)
		{
			case 'view' :
			{
				//Get the document type
				$type   = KFactory::get('lib.joomla.document')->getType();
				$filename = strtolower($name).DS.$type.'.php';
			} break;
			
			default : $filename = strtolower($name).'.php';
		}
		
		return $filename;
	}	
}
<?php
/**
 * @version 	$Id:factory.php 46 2008-03-01 18:39:32Z mjaz $
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
	 * The alias object map
	 *
	 * @var	array
	 */
	protected static $_objectAliasMap = array(
      	'table'     => 'DatabaseTable',
        'row'       => 'DatabaseRow',
      	'rowset'    => 'DatabaseRowset'
	);

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
		$identifier = new KFactoryAdapterComponentIdentifier($identifier);
		if($identifier->extension == 'com') {
			$instance = self::_createInstance($identifier, $options);
		}

		return $instance;
	}

	/**
	 * Get an instance of an instanciatable class
	 *
	 * @param 	KFactoryAdapterComponentIdentifier	Identifier
	 * @param 	array	Object options
	 * @throws	KFactoryAdapterException
	 * @return object
	 */
	protected static function _createInstance(KFactoryAdapterComponentIdentifier $identifier, array $options = array())
	{
		$instance = false;

		$client    = $identifier->application;
		$component = $identifier->component;

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
			else
			{
				$alias = $identifier->type;
				if(array_key_exists($identifier->type, self::$_objectAliasMap)) {
					$alias = self::$_objectAliasMap[$identifier->type];
				}

				if(class_exists( 'K'.ucfirst($alias).$path.ucfirst($identifier->name))) {
					$classname = 'K'.ucfirst($alias).$path.ucfirst($identifier->name);
				} else {
					$classname = 'K'.ucfirst($alias).$path.'Default';
				}
			}
		}

		if(class_exists( $classname ))
		{
			//Create the name suffix.
			$suffix = !empty($identifier->path) ? strtolower($identifier->path).'_'.$identifier->name : $identifier->name;

			$options['name'] = array('prefix' => $identifier->component, 'base' => $identifier->type, 'suffix' => $suffix);
			$instance = new $classname($options);
		}

		return $instance;
	}


}
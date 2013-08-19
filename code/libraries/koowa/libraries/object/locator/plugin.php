<?php
/**
 * @package		Koowa_Service
 * @subpackage 	Locator
 * @copyright	Copyright (C) 2007 - 2012 Johan Janssens. All rights reserved.
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 */

/**
 * Plugin Service Locator
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Koowa\Library\Service
 */
class KObjectLocatorPlugin extends KObjectLocatorAbstract
{
    /**
	 * The type
	 *
	 * @var string
	 */
	protected $_type = 'plg';

	/**
	 * Get the classname based on an identifier
	 *
	 * @param  KObjectIdentifier $identifier An identifier object - plg.type.plugin.[.path].name
	 * @return string|boolean  Return object on success, returns FALSE on failure
	 */
	public function findClass(KObjectIdentifier $identifier)
	{
	    $classpath = KStringInflector::camelize(implode('_', $identifier->path));
		$classname = 'Plg'.ucfirst($identifier->package).$classpath.ucfirst($identifier->name);

		//Don't allow the auto-loader to load plugin classes if they don't exists yet
		if (!class_exists( $classname)) {
			$classname = false;
		}

		return $classname;
	}

	/**
	 * Get the path based on an identifier
	 *
	 * @param  KObjectIdentifier $identifier An Identifier object - plg.type.plugin.[.path].name
	 * @return string|boolean		Returns the path on success FALSE on failure
	 */
	public function findPath(KObjectIdentifier $identifier)
	{
	    $path  = '';
	    $parts = $identifier->path;

		$type  = $identifier->package;

		if(!empty($identifier->name))
		{
			if(count($parts))
			{
				$path    = array_shift($parts).
				$path   .= count($parts) ? '/'.implode('/', $parts) : '';
				$path   .= '/'.strtolower($identifier->name);
			}
			else $path  = strtolower($identifier->name);
		}

		//Plugins can have their own folder
		if (is_file($identifier->basepath.'/plugins/'.$type.'/'.$path.'/'.$path.'.php')) {
		    $path = $identifier->basepath.'/plugins/'.$type.'/'.$path.'/'.$path.'.php';
	    } else {
		    $path = $identifier->basepath.'/plugins/'.$type.'/'.$path.'.php';
		}

		return $path;
	}
}

<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright	Copyright (C) 2007 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/koowa for the canonical source repository
 */

/**
 * Plugin Object Locator
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Koowa\Library\Object
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

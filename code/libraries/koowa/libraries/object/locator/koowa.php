<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright	Copyright (C) 2007 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/koowa for the canonical source repository
 */

/**
 * Koowa Object Locator
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Koowa\Library\Object
 */
class KObjectLocatorKoowa extends KObjectLocatorAbstract
{
	/**
	 * The type
	 *
	 * @var string
	 */
	protected $_type = 'koowa';

	/**
	 * Get the classname based on an identifier
	 *
	 * @param 	KObjectIdentifier $identifier An identifier object - koowa:[path].name
	 * @return string|boolean  Return object on success, returns FALSE on failure
	 */
	public function findClass(KObjectIdentifier $identifier)
	{
        $classname = 'K'.ucfirst($identifier->package).KStringInflector::implode($identifier->path).ucfirst($identifier->name);

		if (!class_exists($classname))
		{
			// use default class instead
			$classname = 'K'.ucfirst($identifier->package).KStringInflector::implode($identifier->path).'Default';

			if (!class_exists($classname)) {
				$classname = false;
			}
		}

		return $classname;
	}

	/**
	 * Get the path based on an identifier
	 *
	 * @param  KObjectIdentifier $identifier An identifier object - koowa:[path].name
	 * @return string	Returns the path
	 */
	public function findPath(KObjectIdentifier $identifier)
	{
	    $path = '';

	    if(count($identifier->path)) {
			$path .= implode('/',$identifier->path);
		}

		if(!empty($identifier->name)) {
			$path .= '/'.$identifier->name;
		}

		$path = $identifier->basepath.'/'.$path.'.php';
		return $path;
	}
}

<?php
/**
 * @package		Koowa_Service
 * @subpackage 	Locator
 * @copyright	Copyright (C) 2007 - 2012 Johan Janssens. All rights reserved.
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 */

/**
 * Koowa Service Locator
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Koowa\Library\Service
 */
class KServiceLocatorKoowa extends KServiceLocatorAbstract
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
	 * @param 	KServiceIdentifier $identifier An identifier object - koowa:[path].name
	 * @return string|false  Return object on success, returns FALSE on failure
	 */
	public function findClass(KServiceIdentifier $identifier)
	{
        $classname = 'K'.ucfirst($identifier->package).KInflector::implode($identifier->path).ucfirst($identifier->name);

		if (!class_exists($classname))
		{
			// use default class instead
			$classname = 'K'.ucfirst($identifier->package).KInflector::implode($identifier->path).'Default';

			if (!class_exists($classname)) {
				$classname = false;
			}
		}

		return $classname;
	}

	/**
	 * Get the path based on an identifier
	 *
	 * @param  KServiceIdentifier $identifier An identifier object - koowa:[path].name
	 * @return string	Returns the path
	 */
	public function findPath(KServiceIdentifier $identifier)
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

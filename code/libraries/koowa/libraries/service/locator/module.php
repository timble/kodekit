<?php
/**
 * @package		Koowa_Service
 * @subpackage 	Locator
 * @copyright	Copyright (C) 2007 - 2012 Johan Janssens. All rights reserved.
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 */

/**
 * Module Service Locator
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Koowa\Library\Service
 */
class KServiceLocatorModule extends KServiceLocatorAbstract
{
	/**
	 * The type
	 *
	 * @var string
	 */
	protected $_type = 'mod';

	/**
	 * Get the classname based on an identifier
	 *
	 * This locator will try to create an generic or default classname on the identifier information
	 * if the actual class cannot be found using a predefined fallback sequence.
	 *
	 * Fallback sequence : -> Named Module Specific
	 *                     -> Named Module Default
	 *                     -> Default Module Specific
	 *                     -> Default Module Default
	 *                     -> Framework Specific
	 *                     -> Framework Default
	 *
	 * @param KObjectIdentifier $identifier  An identifier object - mod:[//application/]module.[.path].name
	 * @return string|boolean  Return object on success, returns FALSE on failure
	 */
	public function findClass(KObjectIdentifier $identifier)
	{
	    $path = KStringInflector::camelize(implode('_', $identifier->path));
		$classname = 'Mod'.ucfirst($identifier->package).$path.ucfirst($identifier->name);

		//Don't allow the auto-loader to load module classes if they don't exists yet
		if (!$this->getService('koowa:class.loader')->loadClass($classname, $identifier->basepath))
		{
			$classpath = $identifier->path;
			$classtype = !empty($classpath) ? array_shift($classpath) : 'view';

			//Create the fallback path and make an exception for views
			$com_path = ($classtype != 'view') ? ucfirst($classtype).KStringInflector::camelize(implode('_', $classpath)) : ucfirst($classtype);
			$mod_path = ($classtype != 'view') ? ucfirst($classtype).KStringInflector::camelize(implode('_', $classpath)) : '';

			/*
			 * Find the classname to fallback too and auto-load the class
			 *
			 * Fallback sequence : -> Named Module Specific
			 *                     -> Named Module Default
			 *                     -> Default Module Specific
			 *                     -> Default Module Default
			 *                     -> Default Component Specific
			 *                     -> Default Component Default
			 *                     -> Framework Specific
			 *                     -> Framework Default
			 */
			if(class_exists('Mod'.ucfirst($identifier->package).$mod_path.ucfirst($identifier->name))) {
				$classname = 'Mod'.ucfirst($identifier->package).$mod_path.ucfirst($identifier->name);
			} elseif(class_exists('Mod'.ucfirst($identifier->package).$mod_path.'Default')) {
				$classname = 'Mod'.ucfirst($identifier->package).$mod_path.'Default';
			} elseif(class_exists('ModKoowa'.$mod_path.ucfirst($identifier->name))) {
				$classname = 'ModKoowa'.$mod_path.ucfirst($identifier->name);
			} elseif(class_exists('ModKoowa'.$mod_path.'Default')) {
				$classname = 'ModKoowa'.$mod_path.'Default';
			} elseif(class_exists('ComKoowa'.$com_path.ucfirst($identifier->name))) {
				$classname = 'ComKoowa'.$com_path.ucfirst($identifier->name);
			} elseif(class_exists('ComKoowa'.$com_path.'Default')) {
				$classname = 'ComKoowa'.$com_path.'Default';
			} elseif(class_exists( 'K'.$com_path.ucfirst($identifier->name))) {
				$classname = 'K'.$com_path.ucfirst($identifier->name);
			} elseif(class_exists('K'.$com_path.'Default')) {
				$classname = 'K'.$com_path.'Default';
			} else {
				$classname = false;
			}

		}

		return $classname;
	}

	/**
	 * Get the path based on an identifier
	 *
	 * @param  KObjectIdentifier $identifier An identifier object - mod:[//application/]module.[.path].name
	 * @return string	Returns the path
	 */
	public function findPath(KObjectIdentifier $identifier)
	{
		$path  = '';
	    $parts = $identifier->path;
		$name  = $identifier->package;

		if(!empty($identifier->name))
		{
			if(count($parts))
			{
				$path    = KStringInflector::pluralize(array_shift($parts)).
				$path   .= count($parts) ? '/'.implode('/', $parts) : '';
				$path   .= '/'.strtolower($identifier->name);
			}
			else $path  = strtolower($identifier->name);
		}

		$path = $identifier->basepath.'/modules/mod_'.$name.'/'.$path.'.php';
	    return $path;
	}
}

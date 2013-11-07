<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright	Copyright (C) 2007 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/koowa for the canonical source repository
 */

/**
 * Component Object Locator
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Koowa\Library\Object
 */
class KObjectLocatorComponent extends KObjectLocatorAbstract
{
	/**
	 * The type
	 *
	 * @var string
	 */
	protected $_type = 'com';

	/**
	 * Get the classname based on an identifier
	 *
	 * This locator will try to create an generic or default classname on the identifier information
	 * if the actual class cannot be found using a predefined fallback sequence.
	 *
	 * Fallback sequence : -> Named Component Specific
	 *                     -> Named Component Default
	 *                     -> Default Component Specific
	 *                     -> Default Component Default
	 *                     -> Framework Specific
	 *                     -> Framework Default
	 *
	 * @param KObjectIdentifier $identifier An identifier object - com:[//application/]component.view.[.path].name
	 * @return string|boolean  Return object on success, returns FALSE on failure
	 */
	public function findClass(KObjectIdentifier $identifier)
	{
	    $path      = KStringInflector::camelize(implode('_', $identifier->path));
        $classname = 'Com'.ucfirst($identifier->package).$path.ucfirst($identifier->name);

      	//Manually load the class to set the basepath
		if (!$this->getObject('koowa:class.loader')->loadClass($classname, $identifier->basepath))
		{
		    $classpath = $identifier->path;
			$classtype = !empty($classpath) ? array_shift($classpath) : '';

			//Create the fallback path and make an exception for views
			$path = ($classtype != 'view') ? ucfirst($classtype).KStringInflector::camelize(implode('_', $classpath)) : ucfirst($classtype);

			/*
			 * Find the classname to fallback too and auto-load the class
			 *
			 * Fallback sequence : -> Named Component Specific
			 *                     -> Named Component Default
			 *                     -> Default Component Specific
			 *                     -> Default Component Default
			 *                     -> Framework Specific
			 *                     -> Framework Default
			 */
			if(class_exists('Com'.ucfirst($identifier->package).$path.ucfirst($identifier->name))) {
				$classname = 'Com'.ucfirst($identifier->package).$path.ucfirst($identifier->name);
			} elseif(class_exists('Com'.ucfirst($identifier->package).$path.'Default')) {
				$classname = 'Com'.ucfirst($identifier->package).$path.'Default';
			} elseif(class_exists('ComKoowa'.$path.ucfirst($identifier->name))) {
				$classname = 'ComKoowa'.$path.ucfirst($identifier->name);
			} elseif(class_exists('ComKoowa'.$path.'Default')) {
				$classname = 'ComKoowa'.$path.'Default';
			} elseif(class_exists( 'K'.$path.ucfirst($identifier->name))) {
				$classname = 'K'.$path.ucfirst($identifier->name);
			} elseif(class_exists('K'.$path.'Default')) {
				$classname = 'K'.$path.'Default';
			} else {
				$classname = false;
			}
		}

		return $classname;
	}

	/**
	 * Get the path based on an identifier
	 *
	 * @param  KObjectIdentifier $identifier  An identifier object - com:[//application/]component.view.[.path].name
	 * @return string	Returns the path
	 */
    public function findPath(KObjectIdentifier $identifier)
    {
        $path  = '';
        $parts = $identifier->path;

        $component = 'com_'.strtolower($identifier->package);

        if(!empty($identifier->name))
        {
            if(count($parts))
            {
                if($parts[0] === 'view') {
                    $parts[0] = KStringInflector::pluralize($parts[0]);
                }

                $path = implode('/', $parts).'/'.strtolower($identifier->name);
            }
            else $path  = strtolower($identifier->name);
        }

        $path = $identifier->basepath.'/components/'.$component.'/'.$path.'.php';
        return $path;
    }
}

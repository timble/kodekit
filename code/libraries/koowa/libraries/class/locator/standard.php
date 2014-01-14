<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright	Copyright (C) 2007 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/koowa for the canonical source repository
 */

/**
 * Standard Class Locator
 *
 * PSR-0 compliant autoloader. Allows autoloading of namespaced classes.
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Koowa\Library\Class
 */
class KClassLocatorStandard extends KClassLocatorAbstract
{
    /**
     * The type
     *
     * @var string
     */
    protected $_type = 'psr';

    /**
     * Get the path based on a class name
     *
     * @param  string   $classname The class name
     * @param  string $basepath    The base path
     * @return string|false   Returns canonicalized absolute pathname or FALSE of the class could not be found.
     */
	public function locate($classname, $basepath = null)
	{
        //Find the class
        foreach($this->_namespaces as $namespace => $basepath)
        {
            if(strpos($classname, $namespace) !== 0) {
                continue;
            }

            if ($pos = strrpos($classname, '\\'))
            {
                $namespace = substr($classname, 0, $pos);
                $class     = substr($classname, $pos + 1);
            }

            $path  = str_replace('\\', DIRECTORY_SEPARATOR, $namespace) . DIRECTORY_SEPARATOR;
            $path .= str_replace('_', DIRECTORY_SEPARATOR, $classname) . '.php';

            return $basepath.'/'.$path;
        }

        return false;
	}
}
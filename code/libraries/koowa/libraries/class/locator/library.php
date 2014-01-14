<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright	Copyright (C) 2007 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/koowa for the canonical source repository
 */

/**
 * Library Class Locator
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Koowa\Library\Loader
 */
class KClassLocatorLibrary extends KClassLocatorAbstract
{
	/**
	 * The adapter type
	 *
	 * @var string
	 */
	protected $_type = 'lib';

	/**
	 * Get the path based on a class name
	 *
     * @param  string $classname    The class name
     * @param  string $basepath     The base path
	 * @return string|boolean		Returns the path on success FALSE on failure
	 */
	public function locate($classname, $basepath = null)
	{
        foreach($this->_namespaces as $namespace => $basepath)
        {
            if(strpos($classname, $namespace) !== 0) {
                continue;
            }

            /*
             * Exception rule for Exception classes
             *
             * Transform class to lower case to always load the exception class from the /exception/ folder.
             */
            if ($pos = strpos($classname, 'Exception'))
            {
                $filename  = substr($classname, $pos + strlen('Exception'));
                $classname = str_replace($filename, ucfirst(strtolower($filename)), $classname);
            }

            $word  = preg_replace('/(?<=\\w)([A-Z])/', ' \\1',  $classname);
            $parts = explode(' ', $word);

            // Remove the K prefix
            array_shift($parts);

		    $path = strtolower(implode('/', $parts));

			if(count($parts) == 1) {
				$path = $path.'/'.$path;
			}

            $file = $basepath.'/'.$path.'.php';
            if(!is_file($file)) {
                $file = $basepath.'/'.$path.'/'.strtolower(array_pop($parts)).'.php';
            }

            return $file;
		}

		return false;
	}
}

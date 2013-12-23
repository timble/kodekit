<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright	Copyright (C) 2007 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/koowa for the canonical source repository
 */

/**
 * Koowa Class Locator
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Koowa\Library\Loader
 */
class KClassLocatorKoowa extends KClassLocatorAbstract
{
	/**
	 * The adapter type
	 *
	 * @var string
	 */
	protected $_type = 'koowa';

	/**
	 * The class prefix
	 *
	 * @var string
	 */
	protected $_prefix = 'K';

	/**
	 * Get the path based on a class name
	 *
     * @param  string $classname    The class name
     * @param  string $basepath     The base path
	 * @return string|boolean		Returns the path on success FALSE on failure
	 */
	public function locate($classname, $basepath = null)
	{
		$path = false;

		// If class start with a 'K' it is a Koowa framework class and we handle it
        if (substr($classname, 0, strlen($this->_prefix)) === $this->_prefix)
        {
            /*
             * Exception rule for Exception classes
             *
             * Transform class to lower case to always load the exception class from the /exception/ folder.
             */
            if ($pos = strpos($classname, 'Exception')) {
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

			if(!is_file($this->_basepath.'/'.$path.'.php')) {
				$path = $path.'/'.strtolower(array_pop($parts));
			}

			$path = $this->_basepath.'/'.$path.'.php';
		}

		return $path;
	}
}

<?php
/**
 * @package		Koowa_Loader
 * @subpackage 	Adapter
 * @copyright	Copyright (C) 2007 - 2012 Johan Janssens. All rights reserved.
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 */

/**
 * Loader Adapter for the Koowa framework
 *
 * @author		Johan Janssens <johan@nooku.org>
 * @package     Koowa_Loader
 * @subpackage 	Adapter
 * @uses 		Koowa
 */
class KLoaderAdapterKoowa extends KLoaderAdapterAbstract
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
	 * @return string|false		Returns the path on success FALSE on failure
	 */
	public function findPath($classname, $basepath = null)
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

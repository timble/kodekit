<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright	Copyright (C) 2007 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/koowa for the canonical source repository
 */

/**
 * Module Class Locator
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Koowa\Component\Koowa
 */
class ComKoowaClassLocatorModule extends KClassLocatorAbstract
{
	/**
	 * The adapter type
	 *
	 * @var string
	 */
	protected $_type = 'mod';

    /**
     * The active basepath
     *
     * @var string
     */
    protected $_basepath;

	/**
	 * Get the path based on a class name
	 *
	 * @param  string $classname    The class name
     * @param  string $basepath     The base path
	 * @return string|boolean		Returns the path on success FALSE on failure
	 */
	public function locate($classname, $basepath = null)
	{
        if (substr($classname, 0, 3) === 'Mod')
        {
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

            $word  = strtolower(preg_replace('/(?<=\\w)([A-Z])/', ' \\1', $classname));
            $parts = explode(' ', $word);

            array_shift($parts);
            $package   = array_shift($parts);
            $namespace = ucfirst($package);

            $module = 'mod_'.$package;
            $file 	= array_pop($parts);

            if(count($parts))
            {
                if($parts[0] === 'view') {
                    $parts[0] = KStringInflector::pluralize($parts[0]);
                }

                $path = implode('/', $parts);
                $path = $path.'/'.$file;
            }
            else $path = $file;

            //Switch basepath
            if(!$this->getNamespace($namespace))
            {
                if(!empty($basepath)) {
                    $this->_basepath = $basepath;
                } else {
                    $basepath = $this->_basepath;
                }
            }
            else $basepath = $this->getNamespace($namespace);

            return $basepath.'/modules/'.$module.'/'.$path.'.php';
		}

		return false;

	}
}

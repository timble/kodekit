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
 * Module class names are case sensitive and uses a Upper Camel Case or Pascal Case naming convention. Module class
 * names can be namespaced based on the component name to allow loading component classes from different locations. If
 * no namespace is registered for a module the class will be located within the active base path.
 *
 * Class names need to be prefixed with 'Mod'. Each folder in the file structure is represented in the class name.
 *
 * Format : Mod[Name][Path][To][File]
 *
 * An exception is made for Exception class names. Exception class names are only party case sensitive. The part after
 * the word 'Exception' is transformed to lower case. Exceptions are loaded from the .../Exception folder relative to
 * their path.
 *
 * Format : Mod[Name][Path][To]Exception[FileNameForException]
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
	 * @param  string $class    The class name
     * @param  string $basepath The base path
	 * @return string|boolean	Returns the path on success FALSE on failure
	 */
	public function locate($class, $basepath = null)
	{
        if (substr($class, 0, 3) === 'Mod')
        {
            /*
             * Exception rule for Exception classes
             *
             * Transform class to lower case to always load the exception class from the /exception/ folder.
             */
            if ($pos = strpos($class, 'Exception'))
            {
                $filename = substr($class, $pos + strlen('Exception'));
                $class    = str_replace($filename, ucfirst(strtolower($filename)), $class);
            }

            $word  = strtolower(preg_replace('/(?<=\\w)([A-Z])/', ' \\1', $class));
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

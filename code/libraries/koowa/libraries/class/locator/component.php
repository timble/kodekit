<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright	Copyright (C) 2007 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/koowa for the canonical source repository
 */

/**
 * Component Class Locator
 *
 * Component class names are case sensitive and uses an Upper Camel Case or Pascal Case naming convention. Component
 * class names can be namespaced based on the component name to allow loading component classes from different base
 * paths, if no namespace is registered for a component the class will be located within the active base path.
 *
 * Class names need to be prefixed with 'Com'. Each folder in the file structure must be represented in the class name.
 *
 * Format : Com[Name][Path][To][File]
 *
 * An exception is made for exception class names. Exception class names are only party case sensitive. The part after
 * the word 'Exception' is transformed to lower case.  Exceptions are loaded from the .../Exception folder relative to
 * their path.
 *
 * Format : Com[Name][Path][To]Exception[FileNameForException]
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Koowa\Library\Loader
 */
class KClassLocatorComponent extends KClassLocatorAbstract
{
	/**
	 * The adapter type
	 *
	 * @var string
	 */
	protected $_type = 'com';

    /**
     * The active basepath
     *
     * @var string
     */
    protected $_basepath;

	/**
	 * Get the path based on a class name
	 *
	 * @param  string $class     The class name
     * @param  string $basepath  The base path
	 * @return string|bool  	 Returns the path on success FALSE on failure
	 */
	public function locate($class, $basepath = null)
	{
        //Find the class
        if (substr($class, 0, 3) === 'Com')
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

            $word    = strtolower(preg_replace('/(?<=\\w)([A-Z])/', ' \\1', $class));
            $parts   = explode(' ', $word);

            array_shift($parts);
            $package   = array_shift($parts);
            $namespace = ucfirst($package);

            $component = 'com_'.$package;
            $file 	   = array_pop($parts);

            if(count($parts))
            {
                if($parts[0] === 'view') {
                    $parts[0] = KStringInflector::pluralize($parts[0]);
                }

                $path = implode('/', $parts).'/'.$file;
            }
            else
            {
                //Exception for framework components. Follow library structure. Don't load classes from root.
                if(isset($this->_namespaces[$namespace])) {
                    $path = $file.'/'.$file;
                } else {
                    $path = $file;
                }
            }

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

            return $basepath.'/components/'.$component.'/'.$path.'.php';
        }

		return false;
	}
}

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
	 * @param  string $classname The class name
     * @param  string $basepath  The base path
	 * @return string|bool  	 Returns the path on success FALSE on failure
	 */
	public function locate($classname, $basepath = null)
	{
        //Find the class
        if (substr($classname, 0, 3) === 'Com')
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

            $word    = strtolower(preg_replace('/(?<=\\w)([A-Z])/', ' \\1', $classname));
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

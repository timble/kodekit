<?php
/**
 * Nooku Framework - http://nooku.org/framework
 *
 * @copyright   Copyright (C) 2007 - 2014 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/nooku/nooku-framework for the canonical source repository
 */

/**
 * Plugin Class Locator
 *
 * Plugin class names are case sensitive and use an Upper Camel Case or Pascal Case naming convention. Plugin class
 * names can be namespaced based on the plugin name to allow loading plugins classes from different locations. If no
 * namespace is registered for a plugin the class will be located within the global namespace, registered as '\'.
 *
 * Class names need to be prefixed with 'Plg'. Each folder in the file structure is represented in the class name.
 *
 * Format    : Plg[Group][Name][Path][To][File]
 * Location  : .../plugins/group/name/path/to/file.php
 *
 * Exceptions
 *
 * 1. An exception is made for exception class names. Exception class names are only party case sensitive. The part after
 * the word 'Exception' is transformed to lower case. Exceptions are loaded from the .../Exception folder relative to
 * their path.
 *
 * Classname : Plg[Group][Name][Path][To]Exception[FileNameForException]
 * Location  : .../plugins/group/name/path/to/exception/filenameforexception.php
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Koowa\Component\Koowa\Class\Locator
 */
class ComKoowaClassLocatorPlugin extends KClassLocatorAbstract
{
    /**
     * The locator name
     *
     * @var string
     */
    protected static $_name = 'plugin';

    /**
     * Get a fully qualified based on a class name
     *
     * @param  string $classname The class name
     * @param  string $basepath  The base path
     * @return string|boolean    Returns the path on success FALSE on failure
     */
    public function locate($classname, $basepath = null)
    {
        if (substr($classname, 0, 3) === 'Plg')
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

            if(count($parts)) {
                $path = implode('/', $parts);
            } else {
                $path = $package;
            }

            //Switch basepath
            if(!$this->getNamespace($namespace)) {
                $basepath = $this->getNamespace('\\');
            } else {
                $basepath = $this->getNamespace($namespace);
            }

            return $basepath.'/'.$package.'/'.$path.'.php';
        }

        return false;

    }
}

<?php
/**
 * Nooku Framework - http://nooku.org/framework
 *
 * @copyright   Copyright (C) 2007 - 2014 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/nooku/nooku-framework for the canonical source repository
 */

/**
 * Module Class Locator
 *
 * Module class names are case sensitive and uses an Upper Camel Case or Pascal Case naming convention. Module class
 * names can be namespaced based, if no namespace is registered for a component the class will be located from within
 * the active base path. File and folder names must be lower case.
 *
 * Class names need to be prefixed with 'Mom'. Each folder in the file structure must be represented in the class name.
 *
 * Classname : Mom[Name][Path][To][File]
 * Location  : .../modules/mod_name/path/to/file.php
 *
 * Exceptions
 *
 * 1. An exception is made for exception class names. Exception class names are only party case sensitive. The part after
 * the word 'Exception' is transformed to lower case.  Exceptions are loaded from the .../Exception folder relative to
 * their path.
 *
 * Classname : Mod[Name][Path][To]Exception[FileNameForException]
 * Location  : .../modules/mod_foo/path/to/exception/filenameforexception.php
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Koowa\Component\Koowa\Class\Locator
 */
class ComKoowaClassLocatorModule extends KClassLocatorAbstract
{
    /**
     * The locator name
     *
     * @var string
     */
    protected static $_name = 'module';

    /**
     * Get a fully qualified path based on a class name
     *
     * @param  string $class    The class name
     * @param  string $basepath The base path
     * @return string|boolean   Returns the path on success FALSE on failure
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
            if(!$this->getNamespace($namespace)) {
                $basepath = $this->getNamespace('\\');
            } else {
                $basepath = $this->getNamespace($namespace);
            }

            return $basepath.'/'.$module.'/'.$path.'.php';
        }

        return false;
    }
}

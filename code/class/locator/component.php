<?php
/**
 * Kodekit - http://timble.net/kodekit
 *
 * @copyright   Copyright (C) 2007 - 2016 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license     MPL v2.0 <https://www.mozilla.org/en-US/MPL/2.0>
 * @link        https://github.com/timble/kodekit for the canonical source repository
 */

namespace Kodekit\Library;

/**
 * Component Class Locator
 *
 * Component class names are case sensitive and uses an Upper Camel Case or Pascal Case naming convention. Component
 * class names can be namespaced based , if no namespace is registered for a component the class will be located from
 * within the active base path. File and folder names must be lower case.
 *
 * Class names need to be prefixed with 'Com'. Each folder in the file structure must be represented in the class name.
 *
 * Classname : [Name][Path][To][File]
 * Location  : .../name/path/to/file.php
 *
 * Exceptions
 *
 * 1. An exception is made for files where the last segment of the file path and the file name are the same. In this case
 * class name can use a shorter syntax where the last segment of the path is omitted.
 *
 * Location  : .../foo/bar/bar.php
 * Classname : FooBar instead of FooBarBar
 *
 * Note : This only applies to classes that are loaded from a registered component namespace when a class is located in
 * the global namespace it will follow the default rule eg, FooBar will be located in .../foo/bar.php
 *
 * 2. An exception is made for exception class names. Exception class names are only party case sensitive. The part after
 * the word 'Exception' is transformed to lower case.  Exceptions are loaded from the .../Exception folder relative to
 * their path.
 *
 * Classname : [Name][Path][To]Exception[FileNameForException]
 * Location  : .../foo/path/to/exception/filenameforexception.php
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Kodekit\Library\Class\Locator
 */
class ClassLocatorComponent extends ClassLocatorAbstract
{
    /**
     * The locator name
     *
     * @var string
     */
    protected static $_name = 'component';

    /**
     * Get a fully qualified path based on a class name
     *
     * @param  string $class     The class name
     * @return string|bool       Returns the path on success FALSE on failure
     */
    public function locate($class)
    {
        $result = false;

        //Find the class
        foreach($this->getNamespaces() as $namespace => $basepaths)
        {
            if(empty($namespace) && strpos($class, '\\')) {
                continue;
            }

            if(strpos('\\'.$class, '\\'.$namespace) !== 0) {
                continue;
            }

            //Remove the namespace from the class name
            $classname = ltrim(substr($class, strlen($namespace)), '\\');

            /*
             * Exception rule for Exception classes
             *
             * Transform class to lower case to always load the exception class from the /exception/ folder.
             */
            if($pos = strpos($classname, 'Exception'))
            {
                $filename  = substr($classname, $pos + strlen('Exception'));
                $classname = str_replace($filename, ucfirst(strtolower($filename)), $classname);
            }

            $parts = explode(' ', strtolower(preg_replace('/(?<=\\w)([A-Z])/', ' \\1', $classname)));

            $file  = array_pop($parts);

            if(count($parts)){
                $path = implode('/', $parts) . '/' . $file;
            } else {
                $path = $file . '/' . $file;
            }

            $paths = array(
                $path . '.php',
                $path . '/' . $file . '.php'
            );

            foreach($basepaths as $basepath)
            {
                foreach($paths as $path)
                {
                    $result = $basepath . '/' .$path;
                    if (is_file($result)) {
                        break (2);
                    }
                }
            }

            return $result;
        }

        return false;
    }
}

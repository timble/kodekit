<?php
/**
 * Kodekit - http://timble.net/kodekit
 *
 * @copyright   Copyright (C) 2007 - 2016 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/timble/kodekit for the canonical source repository
 */

namespace Kodekit\Library;

/**
 * Library Class Locator
 *
 * Library class names are case sensitive and use an Upper Camel Case or Pascal Case naming convention. Libraries must
 * be namespaced using a class name prefix or namespace. File and folder names must be lower case.
 *
 * Each folder in the file structure must be represented in the class name.
 *
 * Classname : [Namespace][Path][To][File]
 * Location  : namespace/.../path/to/file.php
 *
 *  Exceptions
 *
 * 1. An exception is made for files where the last segment of the file path and the file name are the same. In this case
 * class name can use a shorter syntax where the last segment of the path is omitted.
 *
 * Location  : kodekit/library/foo/bar/bar.php
 * Classname : KFooBar instead KFooBarBar
 *
 * 2. An exception is made for exception class names. Exception class names are only party case sensitive. The part after
 * the word 'Exception' is transformed to lower case.  Exceptions are loaded from the .../Exception folder relative to
 * their path.
 *
 * Classname : [Namespace][Path]Exception[FileNameForException]
 * Location  : namespace/.../path/to/exception/filenameforexception.php
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Kodekit\Library\Class\Locator
 */
class ClassLocatorLibrary extends ClassLocatorAbstract
{
    /**
     * The locator name
     *
     * @var string
     */
    protected static $_name = 'library';

    /**
     * Get a fully qualified path based on a class name
     *
     * @param  string $class   The class name
     * @return string|boolean   Returns the path on success FALSE on failure
     */
    public function locate($class)
    {
        $result = false;

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
            if ($pos = strpos($classname, 'Exception'))
            {
                $filename  = substr($classname, $pos + strlen('Exception'));
                $classname = str_replace($filename, ucfirst(strtolower($filename)), $classname);
            }

            $word  = preg_replace('/(?<=\\w)([A-Z])/', ' \\1',  $classname);
            $parts = explode(' ', $word);

            $path = strtolower(implode('/', $parts));

            if(count($parts) == 1) {
                $path = $path.'/'.$path;
            }

            $paths = array(
                $path . '.php',
                $path.'/'.strtolower(array_pop($parts)).'.php'
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

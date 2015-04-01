<?php
/**
 * Nooku Framework - http://nooku.org/framework
 *
 * @copyright   Copyright (C) 2007 - 2014 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/nooku/nooku-framework for the canonical source repository
 */

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
 * Location  : koowa/libraries/foo/bar/bar.php
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
 * @package Koowa\Library\Class\Locator
 */
class KClassLocatorLibrary extends KClassLocatorAbstract
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
     * @param  string $class     The class name
     * @param  string $basepath  The base path
     * @return string|boolean   Returns the path on success FALSE on failure
     */
    public function locate($class, $basepath = null)
    {
        foreach($this->getNamespaces() as $namespace => $basepath)
        {
            if(empty($namespace) && strpos($class, '\\')) {
                continue;
            }

            if(strpos('\\'.$class, '\\'.$namespace) !== 0) {
                continue;
            }

            //Remove the namespace from the class name
            $class = ltrim(substr($class, strlen($namespace)), '\\');

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

            $word  = preg_replace('/(?<=\\w)([A-Z])/', ' \\1',  $class);
            $parts = explode(' ', $word);

            $path = strtolower(implode('/', $parts));

            if(count($parts) == 1) {
                $path = $path.'/'.$path;
            }

            $file = $basepath.'/'.$path.'.php';
            if(!is_file($file)) {
                $file = $basepath.'/'.$path.'/'.strtolower(array_pop($parts)).'.php';
            }

            return $file;
        }

        return false;
    }
}

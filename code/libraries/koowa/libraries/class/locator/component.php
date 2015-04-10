<?php
/**
 * Nooku Framework - http://nooku.org/framework
 *
 * @copyright   Copyright (C) 2007 - 2014 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/nooku/nooku-framework for the canonical source repository
 */

/**
 * Component Class Locator
 *
 * Component class names are case sensitive and uses an Upper Camel Case or Pascal Case naming convention. Component
 * class names can be namespaced based , if no namespace is registered for a component the class will be located from
 * within the active base path. File and folder names must be lower case.
 *
 * Class names need to be prefixed with 'Com'. Each folder in the file structure must be represented in the class name.
 *
 * Classname : Com[Name][Path][To][File]
 * Location  : .../components/com_name/path/to/file.php
 *
 * Exceptions
 *
 * 1. An exception is made for files where the last segment of the file path and the file name are the same. In this case
 * class name can use a shorter syntax where the last segment of the path is omitted.
 *
 * Location  : .../components/com_foo/bar/bar.php
 * Classname : ComFooBar instead of ComFooBarBar
 *
 * Note : This only applies to classes that are loaded from a registered component namespace when a class is located in
 * the global namespace it will follow the default rule eg, ComFooBar will be located in .../components/com_foo/bar.php
 *
 * 2. An exception is made for exception class names. Exception class names are only party case sensitive. The part after
 * the word 'Exception' is transformed to lower case.  Exceptions are loaded from the .../Exception folder relative to
 * their path.
 *
 * Classname : Com[Name][Path][To]Exception[FileNameForException]
 * Location  : .../components/com_foo/path/to/exception/filenameforexception.php
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Koowa\Library\Class\Locator
 */
class KClassLocatorComponent extends KClassLocatorAbstract
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
     * @param  string $basepath  The base path
     * @return string|bool       Returns the path on success FALSE on failure
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

            if(!count($parts))
            {
                //Exception for framework components. Follow library structure. Don't load classes from root.
                if(isset($this->_namespaces[$namespace])) {
                    $path = $file.'/'.$file;
                } else {
                    $path = $file;
                }
            }
            else $path = implode('/', $parts).'/'.$file;

            //Switch basepath
            if ($this->getNamespace($namespace)) {
                $basepath = $this->getNamespace($namespace);
            } else {
                $basepath = $basepath.'/'.$component;
            }

            $result =  $basepath.'/'.$path.'.php';
            if(!is_file($result)) {
                $result = $basepath.'/'.$path.'/'.$file.'.php';
            }

            // Check plural views/ folder for view classes
            if (!is_file($result) && strpos($result, '/view/') !== false)
            {
                $count         = 1;
                $singular_path = str_replace('view/', 'views/', $path, $count);

                $result =  $basepath.'/'.$singular_path.'.php';
                if(!is_file($result)) {
                    $result = $basepath.'/'.$singular_path.'/'.$file.'.php';
                }
            }

            return $result;
        }

        return false;
    }
}

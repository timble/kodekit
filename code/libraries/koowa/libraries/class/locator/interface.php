<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright	Copyright (C) 2007 - 2014 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/koowa for the canonical source repository
 */

/**
 * Loader Adapter Interface
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Koowa\Library\Loader
 */
interface KClassLocatorInterface
{
    /**
     * Get the path based on a class name
     *
     * @param  string  $classname The class name
     * @param  string  $basepath  The basepath to use to find the class
     * @return string|boolean     Returns the path on success FALSE on failure
     */
    public function locate($classname, $basepath = null);

    /**
	 * Get the type
	 *
	 * @return string	Returns the type
	 */
	public function getType();

    /**
     * Register a namespace
     *
     * @param  string $namespace
     * @param  string $path The location of the namespace
     * @return KClassLocatorInterface
     */
    public function registerNamespace($namespace, $path);

    /**
     * Registers an array of namespaces
     *
     * @param array $namespaces An array of namespaces (namespaces as keys and location as value)
     * @return KClassLocatorInterface
     */
    public function registerNamespaces($namespaces);

    /**
     * Get a the namespace paths
     *
     * @param string $namespace The namespace
     * @return string The namespace path
     */
    public function getNamespace($namespace);

    /**
     * Get the registered namespaces
     *
     * @return array An array with namespaces as keys and path as value
     */
    public function getNamespaces();
}

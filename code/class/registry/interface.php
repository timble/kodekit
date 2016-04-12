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
 * Class Registry Interface
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Kodekit\Library\Class\Registry
 */
interface ClassRegistryInterface
{
    /**
     * Get a class from the registry
     *
     * @param  string $class
     * @return  string  The class path
     */
    public function get($class);

    /**
     * Set a class path in the registry
     *
     * @param  string $class   The class name
     * @param  string $path    The class path
     * @return ClassRegistryInterface
     */
    public function set($class, $path);

    /**
     * Check if a class exists in the registry
     *
     * @param  string $class
     * @return  boolean
     */
    public function has($class);

    /**
     * Remove a class from the registry
     *
     * @param  string $class
     * @return  ClassRegistryInterface
     */
    public function remove($class);

    /**
     * Clears out all classes from the registry
     *
     * @return  ClassRegistryInterface
     */
    public function clear();

    /**
     * Try to find an class path based on a class name
     *
     * @param   string  $class
     * @return  string The class path, or NULL if the class is not registered
     */
    public function find($class);

    /**
     * Register an alias for a class
     *
     * @param string $class
     * @param string $alias
     * @return ClassRegistryInterface
     */
    public function alias($class, $alias);

    /**
     * Get a specific locator from a class namespace
     *
     * @return string|false The name of the locator or FALSE if no locator could be found
     */
    public function getLocator($class);

    /**
     * Set a specific locator based on a class namespace
     *
     * @return  ClassRegistry
     */
    public function setLocator($class, $locator);

    /**
     * Get a list of all the namespaces
     *
     * @return array
     */
    public function getNamespaces();

    /**
     * Get a list of all the class aliases
     *
     * @return array
     */
    public function getAliases();

    /**
     * Get a list of all classes in the registry
     *
     * @return  array  An array of classes
     */
    public function getClasses();
}
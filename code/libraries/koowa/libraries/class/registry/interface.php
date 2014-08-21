<?php
/**
 * Nooku Framework - http://nooku.org/framework
 *
 * @copyright   Copyright (C) 2007 - 2014 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/nooku/nooku-framework for the canonical source repository
 */

/**
 * Class Registry Interface
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Koowa\Library\Class\Registry
 */
interface KClassRegistryInterface
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
     * @param  string $class
     * @param  string $path
     * @return KClassRegistryInterface
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
     * @return  KClassRegistryInterface
     */
    public function remove($class);

    /**
     * Clears out all classes from the registry
     *
     * @return  KClassRegistryInterface
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
     * @return KClassRegistryInterface
     */
    public function alias($class, $alias);

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
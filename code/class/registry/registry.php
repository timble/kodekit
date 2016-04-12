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
 * Class Registry
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Kodekit\Library\Class
 */
class ClassRegistry extends \ArrayObject implements ClassRegistryInterface
{
    /**
     * The class aliases
     *
     * @var  array
     */
    protected $_aliases = array();

    /**
     * The class namespaces by locator
     *
     * @var  array
     */
    protected $_namespaces = array();

    /**
     * Get a class from the registry
     *
     * @param  string $class
     * @return  string  The class path
     */
    public function get($class)
    {
        if($this->offsetExists($class)) {
            $result = $this->offsetGet($class);
        } else {
            $result = null;
        }

        return $result;
    }

    /**
     * Set a class path in the registry
     *
     * @param  string $class   The class name
     * @param  string $path    The class path
     * @return ClassRegistryInterface
     */
    public function set($class, $path)
    {
        $this->offsetSet($class, $path);
        return $this;
    }

    /**
     * Check if a class exists in the registry
     *
     * @param  string $class
     * @return  boolean
     */
    public function has($class)
    {
        return $this->offsetExists($class);
    }

    /**
     * Remove a class from the registry
     *
     * @param  string $class
     * @return  ClassRegistry
     */
    public function remove($class)
    {
        $this->offsetUnset($class);
        return $this;
    }

    /**
     * Clears out all objects from the registry
     *
     * @return  ClassRegistry
     */
    public function clear()
    {
        $this->exchangeArray(array());
        return $this;
    }

    /**
     * Try to find an class path based on a class name
     *
     * @param   string  $class
     * @return  string The class path, or NULL if the class is not registered
     */
    public function find($class)
    {
        //Resolve the real identifier in case an alias was passed
        while(array_key_exists($class, $this->_aliases)) {
            $class = $this->_aliases[$class];
        }

        //Find the identifier
        if($this->offsetExists($class)) {
            $result = $this->offsetGet($class);
        } else {
            $result = null;
        }

        return $result;
    }

    /**
     * Register an alias for a class
     *
     * @param string $class
     * @param string $alias
     * @return ClassRegistry
     */
    public function alias($class, $alias)
    {
        //Don't register the alias if it's the same as the class
        if($alias != $class) {
            $this->_aliases[$alias] = $class;
        }

        return $this;
    }

    /**
     * Get a specific locator from the class namespace
     *
     * @return string|false The name of the locator or FALSE if no locator could be found
     */
    public function getLocator($class)
    {
        $result = false;

        if(!false == $pos = strrpos($class, '\\'))
        {
            $namespace = substr($class, 0, $pos);

            if(isset($this->_namespaces[$namespace])) {
                $result = $this->_namespaces[$namespace];
            }
        }

        return $result;
    }

    /**
     * Set a specific locator based on a class namespace
     *
     * @return  ClassRegistry
     */
    public function setLocator($class, $locator)
    {
        if(!false == $pos = strrpos($class, '\\'))
        {
            $namespace = substr($class, 0, $pos);
            $this->_namespaces[$namespace] = $locator;
        }

        return $this;
    }

    /**
     * Get a list of all the namespaces
     *
     * @return array
     */
    public function getNamespaces()
    {
        return array_keys($this->_namespaces);
    }

    /**
     * Get a list of all the class aliases
     *
     * @return array
     */
    public function getAliases()
    {
        return $this->_aliases;
    }

    /**
     * Get a list of all identifiers in the registry
     *
     * @return  array
     */
    public function getClasses()
    {
        return array_keys($this->getArrayCopy());
    }
}
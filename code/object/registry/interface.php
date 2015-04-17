<?php
/**
 * Nooku Framework - http://nooku.org/framework
 *
 * @copyright   Copyright (C) 2007 - 2014 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/nooku/nooku-framework for the canonical source repository
 */

/**
 * Object Registry Interface
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Koowa\Library\Object\Registry
 */
interface KObjectRegistryInterface
{
    /**
     * Get a an object from the registry
     *
     * @param  KObjectIdentifier|string $identifier An ObjectIdentifier, identifier string
     * @return KObjectInterface   The object
     */
    public function get($identifier);

    /**
     * Set an object in the registry
     *
     * @param  KObjectIdentifier|string $identifier An ObjectIdentifier, identifier string
     * @param  mixed $data
     * @return KObjectIdentifier The object identifier that was set in the registry.
     */
    public function set($identifier, $data = null);

    /**
     * Check if an object exists in the registry
     *
     * @param  KObjectIdentifier|string $identifier An ObjectIdentifier, identifier string
     * @return  boolean
     */
    public function has($identifier);

    /**
     * Remove an object from the registry
     *
     * @param  KObjectIdentifier|string $identifier An ObjectIdentifier, identifier string
     * @return KObjectRegistryInterface
     */
    public function remove($identifier);

    /**
     * Clears out all objects from the registry
     *
     * @return  KObjectRegistryInterface
     */
    public function clear();

    /**
     * Try to find an object based on an identifier string
     *
     * @param   mixed  $identifier
     * @return  KObjectIdentifier  An ObjectIdentifier or NULL if the identifier does not exist.
     */
    public function find($identifier);

    /**
     * Add an alias for an identifier
     *
     * @param  KObjectIdentifier|string $identifier An ObjectIdentifier, identifier string
     * @param  KObjectIdentifier|string $alias      The alias
     * @return KObjectRegistry
     */
    public function alias($identifier, $alias);

    /**
     * Register a class for an identifier
     *
     * @param  KObjectIdentifier|string $identifier An ObjectIdentifier, identifier string
     * @param mixed $alias The alias
     * @return KObjectRegistryInterface
     */
    public function setClass($identifier, $class);

    /**
     * Get the identifier class
     *
     * @param  KObjectIdentifier|string $identifier An ObjectIdentifier, identifier string
     * @return string|false|null  Returns the class name or FALSE if the class could not be found.
     */
    public function getClass($identifier);

    /**
     * Get a list of all the identifier aliases
     *
     * @return array
     */
    public function getClasses();

    /**
     * Get a list of all the identifier aliases
     *
     * @return array
     */
    public function getAliases();

    /**
     * Get a list of all identifiers in the registry
     *
     * @return  array  An array of objects
     */
    public function getIdentifiers();
}
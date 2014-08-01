<?php
/**
 * Nooku Framework - http://nooku.org/framework
 *
 * @copyright	Copyright (C) 2007 - 2014 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		https://github.com/nooku/nooku-framework for the canonical source repository
 */

/**
 * Object Registry Interface
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Koowa\Library\Object
 */
interface KObjectRegistryInterface
{
    /**
     * Get a an object from the registry
     *
     * @param  KObjectIdentifier $identifier
     * @return  KObjectInterface   The object
     */
    public function get(KObjectIdentifier $identifier);

    /**
     * Set an object in the registry
     *
     * @param  KObjectIdentifier $identifier
     * @param  mixed             $data
     * @return KObjectRegistryInterface
     */
    public function set(KObjectIdentifier $identifier, $data = null);

    /**
     * Check if an object exists in the registry
     *
     * @param  KObjectIdentifier $identifier
     * @return  boolean
     */
    public function has(KObjectIdentifier $identifier);

    /**
     * Remove an object from the registry
     *
     * @param  KObjectIdentifier $identifier
     * @return KObjectRegistryInterface
     */
    public function remove(KObjectIdentifier $identifier);

    /**
     * Clears out all objects from the registry
     *
     * @return KObjectRegistryInterface
     */
    public function clear();

    /**
     * Register an alias for an identifier
     *
     * @param KObjectIdentifier  $identifier
     * @param mixed             $alias      The alias
     * @return KObjectRegistry
     */
    public function alias(KObjectIdentifier $identifier, $alias);

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
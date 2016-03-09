<?php
/**
 * Nooku Framework - http://nooku.org/framework
 *
 * @copyright   Copyright (C) 2007 - 2014 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/nooku/nooku-framework for the canonical source repository
 */

/**
 * Traversable Model Entity Interface
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Koowa\Library\Model\Entity
 */
interface KModelEntityComposable extends KModelEntityInterface
{
    /**
     * Insert a new entity
     *
     * This function will either clone a entity prototype or create a new instance of the entity object for each
     * entity being inserted. By default the entity will be cloned. The entity will be stored by it's identity_key
     * if set or otherwise by it's object handle.
     *
     * @param   KModelEntityInterface|array $entity  A ModelEntityInterface object or an array of entity properties
     * @param   string  $status     The entity status
     * @return  KModelEntityComposable
     */
    public function insert($entity, $status = null);

    /**
     * Find an entity in the collection based on a needle
     *
     * This functions accepts either a know position or associative array of property/value pairs
     *
     * @param string $needle The position or the key to search for
     * @return KModelEntityInterface
     */
    public function find($needle);

    /**
     * Removes an entity from the collection
     *
     * The entity will be removed based on it's identity_key if set or otherwise by it's object handle.
     *
     * @param  KModelEntityInterface $entity
     * @return KModelEntityComposite
     * @throws InvalidArgumentException if the object doesn't implement KModelEntityInterface
     */
    public function remove($entity);

    /**
     * Checks if the collection contains a specific entity
     *
     * @param   KModelEntityInterface $entity
     * @return  bool Returns TRUE if the object is in the set, FALSE otherwise
     */
    public function contains($entity);
}
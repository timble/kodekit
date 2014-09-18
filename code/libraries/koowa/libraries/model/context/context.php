<?php
/**
 * Nooku Framework - http://nooku.org/framework
 *
 * @copyright   Copyright (C) 2007 - 2014 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/nooku/nooku-framework for the canonical source repository
 */

/**
 * Model Context
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Koowa\Library\Model\Context
 */
class KModelContext extends KCommand implements KModelContextInterface
{
    /**
     * Set the model state
     *
     * @param KModelState $state
     *
     * @return KModelContext
     */
    public function setState($state)
    {
        return KObjectConfig::set('state', $state);
    }

    /**
     * Get the model data
     *
     * @return array
     */
    public function getState()
    {
        return KObjectConfig::get('state');
    }

    /**
     * Set the model entity
     *
     * @param KModelEntityInterface $entity
     * @return KModelContext
     */
    public function setEntity($entity)
    {
        return KObjectConfig::set('entity', $entity);
    }

    /**
     * Get the model data
     *
     * @return array
     */
    public function getEntity()
    {
        return KObjectConfig::get('entity');
    }

    /**
     * Get the identity key
     *
     * @return mixed
     */
    public function getIdentityKey()
    {
        return KObjectConfig::get('identity_key');
    }

    /**
     * Set the identity key
     *
     * @param mixed $value
     * @return KModelContext
     */
    public function setIdentityKey($value)
    {
        return KObjectConfig::set('identity_key', $value);
    }
}
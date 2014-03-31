<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright	Copyright (C) 2007 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/koowa for the canonical source repository
 */

/**
 * Model Context
 *
 * @author  Johan Janssens <http://nooku.assembla.com/profile/johanjanssens>
 * @package Koowa\Library\Model
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
        $this->set('state', $state);

        return $this;
    }

    /**
     * Get the model data
     *
     * @return array
     */
    public function getState()
    {
        return $this->get('state');
    }

    /**
     * Set the model entity
     *
     * @param KModelEntityInterface $entity
     * @return KModelContext
     */
    public function setEntity($entity)
    {
        $this->set('entity', $entity);

        return $this;
    }

    /**
     * Get the model data
     *
     * @return array
     */
    public function getEntity()
    {
        return $this->get('entity');
    }

    /**
     * Get the identity key
     *
     * @return mixed
     */
    public function getIdentityKey()
    {
        return $this->get('identity_key');
    }

    /**
     * Set the identity key
     *
     * @param mixed $value
     * @return ModelContext
     */
    public function setIdentityKey($value)
    {
        $this->set('identity_key', $value);
        return $this;
    }
}
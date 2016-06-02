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
 * Model Context
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Kodekit\Library\Model\Context
 */
class ModelContext extends Command implements ModelContextInterface
{
    /**
     * Constructor.
     *
     * @param  array|\Traversable  $attributes An associative array or a Traversable object instance
     */
    public function __construct($attributes = array())
    {
        ObjectConfig::__construct($attributes);

        //Set the subject and the name
        if($attributes instanceof ModelContextInterface)
        {
            $this->setSubject($attributes->getSubject());
            $this->setName($attributes->getName());
        }
    }

    /**
     * Set the model state
     *
     * @param ModelStateInterface $state
     * @return ModelContext
     */
    public function setState($state)
    {
        return ObjectConfig::set('state', $state);
    }

    /**
     * Get the model data
     *
     * @return ModelStateInterface
     */
    public function getState()
    {
        return ObjectConfig::get('state');
    }

    /**
     * Get the identity key
     *
     * @return mixed
     */
    public function getIdentityKey()
    {
        return ObjectConfig::get('identity_key');
    }

    /**
     * Set the identity key
     *
     * @param mixed $value
     * @return ModelContext
     */
    public function setIdentityKey($value)
    {
        return ObjectConfig::set('identity_key', $value);
    }
}
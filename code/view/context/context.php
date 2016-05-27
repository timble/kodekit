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
 * View Context
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Kodekit\Library\View\Context
 */
class ViewContext extends Command implements ViewContextInterface
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
        if($attributes instanceof ViewContextInterface)
        {
            $this->setSubject($attributes->getSubject());
            $this->setName($attributes->getName());
        }
    }

    /**
     * Set the model entity
     *
     * @param ModelEntityInterface $entity
     * @return ViewContext
     */
    public function setEntity(ModelEntityInterface $entity)
    {
        return ObjectConfig::set('entity', $entity);
    }

    /**
     * Get the model entity
     *
     * @return ModelEntityInterface
     */
    public function getEntity()
    {
        return ObjectConfig::get('entity');
    }

    /**
     * Set the view data
     *
     * @param array $data
     * @return ViewContext
     */
    public function setData($data)
    {
        return ObjectConfig::set('data', $data);
    }

    /**
     * Get the view data
     *
     * @return array
     */
    public function getData()
    {
        return ObjectConfig::get('data');
    }

    /**
     * Set the view parameters
     *
     * @param array $parameters
     * @return ViewContext
     */
    public function setParameters($parameters)
    {
        return ObjectConfig::set('parameters', $parameters);
    }

    /**
     * Get the view parameters
     *
     * @return array
     */
    public function getParameters()
    {
        return ObjectConfig::get('parameters');
    }
}
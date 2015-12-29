<?php
/**
 * Nooku Framework - http://nooku.org/framework
 *
 * @copyright   Copyright (C) 2007 - 2014 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/nooku/nooku-framework for the canonical source repository
 */

/**
 * View Context
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Koowa\Library\View\Context
 */
class KViewContext extends KCommand implements KViewContextInterface
{
    /**
     * Constructor.
     *
     * @param  array|\Traversable  $attributes An associative array or a Traversable object instance
     */
    public function __construct($attributes = array())
    {
        KObjectConfig::__construct($attributes);
    }

    /**
     * Set the view data
     *
     * @param array $data
     * @return KViewContext
     */
    public function setData($data)
    {
        return KObjectConfig::set('data', $data);
    }

    /**
     * Get the view data
     *
     * @return array
     */
    public function getData()
    {
        return KObjectConfig::get('data');
    }

    /**
     * Set the view parameters
     *
     * @param array $parameters
     * @return KViewContextTemplate
     */
    public function setParameters($parameters)
    {
        return KObjectConfig::set('parameters', $parameters);
    }

    /**
     * Get the view parameters
     *
     * @return array
     */
    public function getParameters()
    {
        return KObjectConfig::get('parameters');
    }
}
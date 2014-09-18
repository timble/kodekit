<?php
/**
 * Nooku Framework - http://nooku.org/framework
 *
 * @copyright   Copyright (C) 2007 - 2014 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/nooku/nooku-framework for the canonical source repository
 */

/**
 * Model Interface
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Koowa\Library\Model
 */
interface KModelInterface
{
    /**
     * Create a new entity for the data source
     *
     * @param  array $properties Array of entity properties
     * @return  KModelEntityInterface
     */
    public function create(array $properties = array());

    /**
     * Fetch an entity from the datasource on the model state
     *
     * @return KModelEntityInterface
     */
    public function fetch();

    /**
     * Get the total amount of items
     *
     * @return  int
     */
    public function count();

    /**
     * Reset the model data and state
     *
     * @param  array $modified List of changed state names
     * @return KModelInterface
     */
    public function reset(array $modified = array());

    /**
     * Set the model state values
     *
     * @param  array $values Set the state values
     *
     * @return KModelInterface
     */
    public function setState(array $values);

    /**
     * Method to get state object
     *
     * @return  KModelStateInterface  The model state object
     */
    public function getState();
}
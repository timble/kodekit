<?php
/**
 * Kodekit - http://timble.net/kodekit
 *
 * @copyright   Copyright (C) 2007 - 2016 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/timble/kodekit for the canonical source repository
 */

namespace Kodekit\Library;

/**
 * Model Interface
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Kodekit\Library\Model
 */
interface ModelInterface
{
    /**
     * Create a new entity for the data store
     *
     * @param  array $properties Array of entity properties
     * @return  ModelEntityComposite
     */
    public function create(array $properties = array());

    /**
     * Fetch an entity from the data store using the model state
     *
     * @return ModelEntityComposite
     */
    public function fetch();

    /**
     * Get the total amount of entities from the data store using the model state
     *
     * @return  int
     */
    public function count();

    /**
     * Reset the model data and state
     *
     * @param  array $modified List of changed state names
     * @return ModelInterface
     */
    public function reset(array $modified = array());

    /**
     * Set the model state values
     *
     * @param  array $values Set the state values
     *
     * @return ModelInterface
     */
    public function setState(array $values);

    /**
     * Method to get state object
     *
     * @return  ModelStateInterface  The model state object
     */
    public function getState();
}
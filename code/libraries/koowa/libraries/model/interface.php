<?php
/**
 * Nooku Framework - http://www.nooku.org
 *
 * @copyright      Copyright (C) 2007 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license        GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link           git://git.assembla.com/nooku-framework.git for the canonical source repository
 */

/**
 * Model Interface
 *
 * @author  Johan Janssens <http://nooku.assembla.com/profile/johanjanssens>
 * @package NookuLibraryModel
 */
interface KModelInterface
{
    /**
     * Create a new entity for the data source
     *
     * @return  KModelEntityInterface
     */
    public function create();

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
     * @return KModelInterface
     */
    public function reset();

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
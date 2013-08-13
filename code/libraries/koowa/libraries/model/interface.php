<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright	Copyright (C) 2007 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/koowa for the canonical source repository
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
     * Reset all cached data and reset the model state to it's default
     *
     * @param   boolean $default If TRUE use defaults when resetting. Default is TRUE
     * @return KModelAbstract
     */
    public function reset($default = true);

    /**
     * Method to get state object
     *
     * @return  object  The state object
     */
    public function getState();

    /**
     * Method to get a item
     *
     * @return  KDatabaseRowInterface
     */
    public function getItem();

    /**
     * Get a list of items
     *
     * @return  KDatabaseRowsetInterface
     */
    public function getList();

    /**
     * Get the total amount of items
     *
     * @return  int
     */
    public function getTotal();

	/**
     * Get the model data
     *
     * If the model state is unique this function will call getItem(), otherwise it will call getList().
     *
     * @return KDatabaseRowsetInterface|KDatabaseRowInterface
     */
    public function getData();
}

<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright	Copyright (C) 2007 - 2014 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/koowa for the canonical source repository
 */

/**
 * Database Context Interface
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Koowa\Library\Database
 */
interface KDatabaseContextInterface extends KCommandInterface
{
    /**
     * Get the query object
     *
     * @return KDatabaseQueryInterface|string
     */
    public function getQuery();

    /**
     * Set the query object
     *
     * @param KDatabaseQueryInterface|string $query
     * @return $this
     */
    public function setQuery($query);

    /**
     * Get the number of affected rows
     *
     * @return integer
     */
    public function getAffected();

    /**
     * Get the number of affected rows
     *
     * @param integer $affected
     * @return KDatabaseContext
     */
    public function setAffected($affected);
}
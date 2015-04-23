<?php
/**
 * Nooku Framework - http://nooku.org/framework
 *
 * @copyright   Copyright (C) 2007 - 2014 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/nooku/nooku-framework for the canonical source repository
 */

/**
 * Database Context
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Koowa\Library\Database\Context
 */
class KDatabaseContext extends KCommand implements KDatabaseContextInterface
{
    /**
     * Get the query object
     *
     * @return KDatabaseQueryInterface|string
     */
    public function getQuery()
    {
        return KObjectConfig::get('query');
    }

    /**
     * Set the query object
     *
     * @param KDatabaseQueryInterface|string $query
     * @return KDatabaseContext
     */
    public function setQuery($query)
    {
        return KObjectConfig::set('query', $query);
    }

    /**
     * Get the number of affected rows
     *
     * @return integer
     */
    public function getAffected()
    {
        return KObjectConfig::get('affected');
    }

    /**
     * Get the number of affected rows
     *
     * @param integer $affected
     * @return KDatabaseContext
     */
    public function setAffected($affected)
    {
        return KObjectConfig::set('affected', $affected);
    }
}
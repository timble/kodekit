<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright	Copyright (C) 2007 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/koowa for the canonical source repository
 */

/**
 * Database Context
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Koowa\Library\Database
 */
class KDatabaseContext extends KCommand implements KDatabaseContextInterface
{
    /**
     * Get the request object
     *
     * @return string The database operation
     */
    public function getOperation()
    {
        return $this->get('operation');
    }

    /**
     * Set the database operation
     *
     * @param string $operation
     * @return $this
     */
    public function setOperation($operation)
    {
        $this->set('operation', $operation);
        return $this;
    }

    /**
     * Get the response object
     *
     * @return KDatabaseQueryInterface
     */
    public function getQuery()
    {
        return $this->get('query');
    }

    /**
     * Set the query object
     *
     * @param KDatabaseQueryInterface|string $query
     * @return $this
     */
    public function setQuery($query)
    {
        $this->set('query', $query);
        return $this;
    }
}
<?php
/**
 * Nooku Framework - http://nooku.org/framework
 *
 * @copyright   Copyright (C) 2007 - 2014 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/nooku/nooku-framework for the canonical source repository
 */

/**
 * Database Query Interface
 *
 * @author  Gergo Erdosi <https://github.com/gergoerdosi>
 * @package Koowa\Library\Database\Query
 */
interface KDatabaseQueryInterface
{
    /**
     * Bind values to a corresponding named placeholders in the query.
     *
     * @param  array $parameters Associative array of parameters.
     * @return $this
     */
    public function bind(array $parameters);

    /**
     * Get the query parameters
     *
     * @return KObjectArray
     */
    public function getParameters();

    /**
     * Set the query parameters
     *
     * @param array $parameters  The query parameters
     * @return KDatabaseQueryInterface
     */
    public function setParameters(array $parameters);

    /**
     * Gets the database engine
     *
     * @return \KDatabaseEngineInterface
     */
    public function getEngine();

    /**
     * Set the database engine
     *
     * @param  KDatabaseEngineInterface $engine A KDatabaseEngineInterface object
     * @return KDatabaseQueryInterface
     */
    public function setEngine(KDatabaseEngineInterface $engine);

    /**
     * Render the query to a string.
     *
     * @return  string  The query string.
     */
    public function toString();
}

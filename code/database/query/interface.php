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
 * Database Query Interface
 *
 * @author  Gergo Erdosi <https://github.com/gergoerdosi>
 * @package Kodekit\Library\Database\Query
 */
interface DatabaseQueryInterface
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
     * @return ObjectArray
     */
    public function getParameters();

    /**
     * Set the query parameters
     *
     * @param array $parameters  The query parameters
     * @return DatabaseQueryInterface
     */
    public function setParameters(array $parameters);

    /**
     * Gets the database driver
     *
     * @return DatabaseDriverInterface
     */
    public function getDriver();

    /**
     * Set the database driver
     *
     * @param  DatabaseDriverInterface $driver A DatabaseDriverInterface object
     * @return DatabaseQueryInterface
     */
    public function setDriver(DatabaseDriverInterface $driver);

    /**
     * Render the query to a string.
     *
     * @return  string  The query string.
     */
    public function toString();
}

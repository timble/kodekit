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
 * Database Context Interface
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Kodekit\Library\Database\Context
 */
interface DatabaseContextInterface extends CommandInterface
{
    /**
     * Get the query object
     *
     * @return DatabaseQueryInterface|string
     */
    public function getQuery();

    /**
     * Set the query object
     *
     * @param DatabaseQueryInterface|string $query
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
     * @return DatabaseContext
     */
    public function setAffected($affected);
}
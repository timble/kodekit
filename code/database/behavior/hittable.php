<?php
/**
 * Nooku Framework - http://nooku.org/framework
 *
 * @copyright   Copyright (C) 2007 - 2014 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/nooku/nooku-framework for the canonical source repository
 */

/**
 * Hittable Database Behavior
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Koowa\Library\Database\Behavior
 */
class KDatabaseBehaviorHittable extends KDatabaseBehaviorAbstract
{
    /**
     * Check if the behavior is supported
     *
     * Behavior requires a 'hits'
     *
     * @return  boolean  True on success, false otherwise
     */
    public function isSupported()
    {
        $table = $this->getMixer();

        //Only check if we are connected with a table object, otherwise just return true.
        if($table instanceof KDatabaseTableInterface)
        {
            if(!$table->hasColumn('hits'))  {
                return false;
            }
        }

        return true;
    }

    /**
     * Increase hit counter by 1
     *
     * Requires a 'hits' column
     */
    public function hit()
    {
        $this->hits++;

        if(!$this->isNew()) {
            $this->save();
        }

        return $this->getMixer();
    }
}

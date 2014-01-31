<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright	Copyright (C) 2007 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/koowa for the canonical source repository
 */

/**
 * Hittable Database Behavior
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Koowa\Library\Database
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
        $mixer = $this->getMixer();
        if($mixer instanceof KDatabaseRowInterface && ($mixer->has('hits')))  {
            return true;
        }

        return parent::isSupported();
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

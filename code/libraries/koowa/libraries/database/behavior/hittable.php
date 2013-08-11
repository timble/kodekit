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
     * Get the methods that are available for mixin based
     *
     * This function conditionally mixes the behavior. Only if the mixer
     * has a 'hits' property the behavior will be mixed in.
     *
     * @param KObject $mixer The mixer requesting the mixable methods.
     * @return array An array of methods
     */
    public function getMixableMethods(KObject $mixer = null)
    {
        $methods = array();

        if(isset($mixer->hits)) {
            $methods = parent::getMixableMethods($mixer);
        }

        return $methods;
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

        return $this->_mixer;
    }
}

<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright	Copyright (C) 2007 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/koowa for the canonical source repository
 */

/**
 * Persistable Dispatcher Behavior
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Koowa\Library\Dispatcher
 */
class ComKoowaDispatcherBehaviorPersistable extends KDispatcherBehaviorPersistable
{
    /**
     * Get an object handle
     *
     * @return string A string that is unique, or NULL
     * @see execute()
     */
    public function getHandle()
    {
        //Disable controller persistency on requests containing the tmpl variable set to component (modal boxes)
        if (KRequest::get('get.tmpl','cmd') != 'component') {
            return parent::getHandle();
        }

        return null;
    }
}
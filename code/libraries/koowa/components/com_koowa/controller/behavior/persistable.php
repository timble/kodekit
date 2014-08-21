<?php
/**
 * Nooku Framework - http://nooku.org/framework
 *
 * @copyright   Copyright (C) 2007 - 2014 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/nooku/nooku-framework for the canonical source repository
 */

/**
 * Persistable Controller Behavior
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Koowa\Component\Koowa\Controller\Behavior
 */
class ComKoowaControllerBehaviorPersistable extends KControllerBehaviorPersistable
{
    /**
     * Check if the behavior is supported
     *
     * @return  boolean  True on success, false otherwise
     */
    public function isSupported()
    {
        $mixer = $this->getMixer();

        //Disable controller persistency on requests containing the tmpl variable set to component (modal boxes)
        if ($mixer->getRequest()->query->get('tmpl', 'cmd') === 'component') {
            return false;
        }

        return parent::isSupported();
    }
}
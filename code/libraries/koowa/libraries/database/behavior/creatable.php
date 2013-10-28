<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright	Copyright (C) 2007 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/koowa for the canonical source repository
 */

/**
 * Creatable Database Behavior
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Koowa\Library\Database
 */
class KDatabaseBehaviorCreatable extends KDatabaseBehaviorAbstract
{
    /**
     * Get the methods that are available for mixin based
     *
     * This function conditionally mixes the behavior. Only if the mixer has a 'created_by' or 'created_on' property
     * the behavior will be mixed in.
     *
     * @param KObjectMixable $mixer The mixer requesting the mixable methods.
     * @return array         An array of methods
     */
    public function getMixableMethods(KObjectMixable $mixer = null)
    {
        $methods = array();

        if(isset($mixer->created_by) || isset($mixer->created_on))  {
            $methods = parent::getMixableMethods($mixer);
        }

        return $methods;
    }

    /**
     * Set created information
     *
     * Requires an 'created_on' and 'created_by' column
     *
     * @param KCommand $context
     * @return void
     */
    protected function _beforeInsert(KCommand $context)
    {
        if(isset($this->created_by) && empty($this->created_by)) {
            $this->created_by  = (int) JFactory::getUser()->get('id');
        }

        if(isset($this->created_on) && (empty($this->created_on) || $this->created_on == $context->caller->getDefault('created_on'))) {
            $this->created_on  = gmdate('Y-m-d H:i:s');
        }
    }
}

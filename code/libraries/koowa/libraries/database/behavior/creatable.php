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
     * Get the user that created the resource
     *
     * @return KUserInterface|null Returns a User object or NULL if no user could be found
     */
    public function getAuthor()
    {
        $user = null;

        if($this->hasProperty('created_by') && !empty($this->created_by)) {
            $user = $this->getObject('user.provider')->fetch($this->created_by);
        }

        return $user;
    }

    /**
     * Check if the behavior is supported
     *
     * Behavior requires a 'created_by' or 'created_on' row property
     *
     * @return  boolean  True on success, false otherwise
     */
    public function isSupported()
    {
        $mixer = $this->getMixer();
        $table = $mixer instanceof KDatabaseRowInterface ?  $mixer->getTable() : $mixer;

        if($table->hasColumn('created_by') || $table->hasColumn('created_on'))  {
            return true;
        }

        return false;
    }

    /**
     * Set created information
     *
     * Requires an 'created_on' and 'created_by' column
     *
     * @param KDatabaseContext	$context A database context object
     * @return void
     */
    protected function _beforeInsert(KDatabaseContext $context)
    {
        $mixer = $this->getMixer();
        $table = $mixer instanceof KDatabaseRowInterface ?  $mixer->getTable() : $mixer;

        if($this->hasProperty('created_by') && empty($this->created_by)) {
            $this->created_by  = (int) $this->getObject('user')->getId();
        }

        if($this->hasProperty('created_on') && (empty($this->created_on) || $this->created_on == $table->getDefault('created_on'))) {
            $this->created_on  = gmdate('Y-m-d H:i:s');
        }
    }
}

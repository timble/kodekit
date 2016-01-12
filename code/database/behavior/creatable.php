<?php
/**
 * Nooku Framework - http://nooku.org/framework
 *
 * @copyright   Copyright (C) 2007 - 2014 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/nooku/nooku-framework for the canonical source repository
 */

/**
 * Creatable Database Behavior
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Koowa\Library\Database\Behavior
 */
class KDatabaseBehaviorCreatable extends KDatabaseBehaviorAbstract
{
    /**
     * List of user identifiers to lazy load
     *
     * @var    array
     */
    protected static $_users = array();

    /**
     * Get the user that created the resource
     *
     * @return KUserInterface|null Returns a User object or NULL if no user could be found
     */
    public function getAuthor()
    {
        $user = null;

        if($this->hasProperty('created_by') && !empty($this->created_by)) {
            $user = $this->_getUser($this->created_by);
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
        $table = $this->getMixer();

        //Only check if we are connected with a table object, otherwise just return true.
        if($table instanceof KDatabaseTableInterface)
        {
            if(!$table->hasColumn('created_by') && !$table->hasColumn('created_on'))  {
                return false;
            }
        }

        return true;
    }

    /**
     * Get a user
     *
     * @return KUserInterface
     */
    protected function _getUser($identifier)
    {
        //Fetch all the users
        if(!empty(static::$_users))
        {
            $this->getObject('user.provider')->fetch(static::$_users);
            static::$_users = array(); //unset the users array
        }

        return $this->getObject('user.provider')->getUser($identifier);
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

    /**
     * Set created information
     *
     * Requires a 'created_by' column
     *
     * @param KDatabaseContext	$context A database context object
     * @return void
     */
    protected function _afterSelect(KDatabaseContext $context)
    {
        $rowset = $context->data;

        if($rowset instanceof KDatabaseRowsetInterface)
        {
            foreach($rowset as $row)
            {
                if(!empty($row->created_by)) {
                    static::$_users[$row->created_by] = $row->created_by;
                }
            }
        }
    }
}

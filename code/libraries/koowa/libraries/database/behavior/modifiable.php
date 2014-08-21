<?php
/**
 * Nooku Framework - http://nooku.org/framework
 *
 * @copyright   Copyright (C) 2007 - 2014 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/nooku/nooku-framework for the canonical source repository
 */

/**
 * Modifiable Database Behavior
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Koowa\Library\Database\Behavior
 */
class KDatabaseBehaviorModifiable extends KDatabaseBehaviorAbstract
{
    /**
     * Initializes the options for the object
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param   KObjectConfig $config Configuration options
     * @return void
     */
    protected function _initialize(KObjectConfig $config)
    {
        $config->append(array(
            'priority'  => self::PRIORITY_LOW,
        ));

        parent::_initialize($config);
    }

    /**
     * Get the user that last edited the resource
     *
     * @return KUserInterface|null Returns a User object or NULL if no user could be found
     */
    public function getEditor()
    {
        $user = null;

        if($this->has('modified_by') && !empty($this->modified_by)) {
            $user = $this->getObject('user.provider')->load($this->modified_by);
        }

        return $user;
    }

    /**
     * Check if the behavior is supported
     *
     * Behavior requires a 'modified_by' or 'modified_by' row property
     *
     * @return  boolean  True on success, false otherwise
     */
    public function isSupported()
    {
        $table = $this->getMixer();

        //Only check if we are connected with a table object, otherwise just return true.
        if($table instanceof KDatabaseTableInterface)
        {
            if(!$table->hasColumn('modified_by') && !$table->hasColumn('modified_on')) {
                return false;
            }
        }

        return true;
    }

    /**
     * Set modified information
     *
     * Requires a 'modified_on' and 'modified_by' column
     *
     * @param KDatabaseContextInterface $context
     * @return void
     */
    protected function _beforeUpdate(KDatabaseContextInterface $context)
    {
        //Get the modified columns
        $modified   = $this->getTable()->filter($this->getProperties(true));

        if(!empty($modified))
        {
            if($this->hasProperty('modified_by')) {
                $this->modified_by = (int) $this->getObject('user')->getId();
            }

            if($this->hasProperty('modified_on')) {
                $this->modified_on = gmdate('Y-m-d H:i:s');
            }
        }
    }
}

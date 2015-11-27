<?php
/**
 * Nooku Framework - http://nooku.org/framework
 *
 * @copyright   Copyright (C) 2007 - 2014 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/nooku/nooku-framework for the canonical source repository
 */

/**
 * Lockable Database Behavior
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Koowa\Component\Koowa\Database\Behavior
 */
class ComKoowaDatabaseBehaviorLockable extends KDatabaseBehaviorLockable
{
    /**
     * Initializes the options for the object
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param   KObjectConfig $config Configuration options
     * @return  void
     */
    protected function _initialize(KObjectConfig $config)
    {
        $config->append(array(
            'lifetime'   =>  JFactory::getSession()->getExpire()
        ));

        parent::_initialize($config);
    }

    /**
     * Get the user that owns the lock on the resource
     *
     * @return KUserInterface Returns a User object
     */
    public function getLocker()
    {
        $provider = $this->getObject('user.provider');

        if($this->hasProperty('locked_by') && !empty($this->locked_by))
        {
            if($this->_owner_id && $this->_owner_id == $this->locked_by
                && !$provider->isLoaded($this->locked_by))
            {
                $data = array(
                    'id'         => $this->_owner_id,
                    'email'      => $this->_owner_email,
                    'name'       => $this->_owner_name,
                    'username'   => $this->_owner_username,
                    'authentic'  => false,
                    'enabled'    => !$this->_owner_block,
                    'expired'    => (bool) $this->_owner_activation,
                    'attributes' => json_decode($this->_owner_params)
                );

                $user = $provider->store($this->_owner_id, $data);
            }
            else $user = $provider->load($this->locked_by);
        }
        else $user = $provider->load(0);

        return $user;
    }

    /**
     * Set created information
     *
     * Requires a 'locked_by' column
     *
     * @param KDatabaseContext  $context A database context object
     * @return void
     */
    protected function _beforeSelect(KDatabaseContext $context)
    {
        if (!$context->query->isCountQuery())
        {
            $context->query
                ->columns(array('_owner_id'         => '_owner.id'))
                ->columns(array('_owner_name'       => '_owner.name'))
                ->columns(array('_owner_username'   => '_owner.username'))
                ->columns(array('_owner_email'      => '_owner.email'))
                ->columns(array('_owner_params'     => '_owner.params'))
                ->columns(array('_owner_block'      => '_owner.block'))
                ->columns(array('_owner_activation' => '_owner.activation'))
                ->columns(array('locked_by_name'    => '_owner.name'))
                ->join(array('_owner' => 'users'), 'tbl.locked_by = _owner.id');
        }

    }
}

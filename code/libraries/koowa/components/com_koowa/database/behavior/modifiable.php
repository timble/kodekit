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
class ComKoowaDatabaseBehaviorModifiable extends KDatabaseBehaviorModifiable
{
    /**
     * Get the user that last edited the resource
     *
     * @return KUserInterface Returns a User object
     */
    public function getEditor()
    {
        $provider = $this->getObject('user.provider');

        if($this->hasProperty('modified_by') && !empty($this->modified_by))
        {
            if($this->_editor_id && $this->_editor_id == $this->modified_by
                && !$provider->isLoaded($this->modified_by))
            {
                $data = array(
                    'id'         => $this->_editor_id,
                    'email'      => $this->_editor_email,
                    'name'       => $this->_editor_name,
                    'username'   => $this->_editor_username,
                    'authentic'  => false,
                    'enabled'    => !$this->_editor_block,
                    'expired'    => (bool) $this->_editor_activation,
                    'attributes' => json_decode($this->_editor_params)
                );

                $user = $provider->store($this->_editor_id, $data);
            }
            else $user = $provider->load($this->modified_by);
        }
        else $user = $provider->load(0);

        return $user;
    }

    /**
     * Set created information
     *
     * Requires a 'modified_by' column
     *
     * @param KDatabaseContext  $context A database context object
     * @return void
     */
    protected function _beforeSelect(KDatabaseContext $context)
    {
        if (!$context->query->isCountQuery())
        {
            $context->query
                ->columns(array('_editor_id' => '_editor.id'))
                ->columns(array('_editor_name' => '_editor.name'))
                ->columns(array('_editor_username' => '_editor.username'))
                ->columns(array('_editor_email' => '_editor.email'))
                ->columns(array('_editor_params' => '_editor.params'))
                ->columns(array('_editor_block' => '_editor.block'))
                ->columns(array('_editor_activation' => '_editor.activation'))
                ->columns(array('modified_by_name' => '_editor.name'))
                ->join(array('_editor' => 'users'), 'tbl.modified_by = _editor.id');
        }
    }
}

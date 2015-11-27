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
class ComKoowaDatabaseBehaviorCreatable extends KDatabaseBehaviorCreatable
{
    /**
     * Get the user that created the resource
     *
     * @return KUserInterface Returns a User object
     */
    public function getAuthor()
    {
        $provider = $this->getObject('user.provider');

        if($this->hasProperty('created_by') && !empty($this->created_by))
        {
            if($this->_author_id && $this->_author_id == $this->created_by
                && !$provider->isLoaded($this->created_by))
            {
                $data = array(
                    'id'         => $this->_author_id,
                    'email'      => $this->_author_email,
                    'name'       => $this->_author_name,
                    'username'   => $this->_author_username,
                    'authentic'  => false,
                    'enabled'    => !$this->_author_block,
                    'expired'    => (bool) $this->_author_activation,
                    'attributes' => json_decode($this->_author_params)
                );

                $user = $provider->store($this->_author_id, $data);
            }
            else $user = $provider->load($this->created_by);
        }
        else $user = $provider->load(0);

        return $user;
    }

    /**
     * Set created information
     *
     * Requires a 'created_by' column
     *
     * @param KDatabaseContext  $context A database context object
     * @return void
     */
    protected function _beforeSelect(KDatabaseContext $context)
    {
        if (!$context->query->isCountQuery())
        {
            $context->query
                ->columns(array('_author_id'         => '_author.id'))
                ->columns(array('_author_name'       => '_author.name'))
                ->columns(array('_author_username'   => '_author.username'))
                ->columns(array('_author_email'      => '_author.email'))
                ->columns(array('_author_params'     => '_author.params'))
                ->columns(array('_author_block'      => '_author.block'))
                ->columns(array('_author_activation' => '_author.activation'))
                ->columns(array('created_by_name'    => '_author.name'))
                ->join(array('_author' => 'users'), 'tbl.created_by = _author.id');
        }
    }
}

<?php
/**
 * Nooku Framework - http://nooku.org/framework
 *
 * @copyright   Copyright (C) 2007 - 2014 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/nooku/nooku-framework for the canonical source repository
 */

/**
 * User
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Koowa\Component\Koowa\User
 */
final class ComKoowaUser extends KUser implements ComKoowaUserInterface
{
    protected function _initialize(KObjectConfig $config)
    {
        $user = JFactory::getUser();

        $config->append(array(
            'data' => array(
                'id'         => $user->id,
                'email'      => $user->email,
                'name'       => $user->name,
                'username'   => $user->username,
                'password'   => $user->password,
                'salt'       => '',
                'authentic'  => !$user->guest,
                'enabled'    => !$user->block,
                'expired'    => !$user->activation,
                'attributes' => $user->getParameters()->toArray()
            )
        ));

        parent::_initialize($config);
    }

    /**
     * Returns the username of the user
     *
     * @return string The name
     */
    public function getUsername()
    {
        return $this->getSession()->get('user.username');
    }

    /**
     * Method to get a parameter value
     *
     * @param   string  $key      Parameter key
     * @param   mixed   $default  Parameter default value
     * @return  mixed  The value or the default if it did not exist
     */
    public function getParameter($key, $default = null)
    {
        return JFactory::getUser()->getParam($key, $default);
    }

    /**
     * Returns the roles of the user
     *
     * @return int The role id
     */
    public function getRoles()
    {
        $data  = $this->getData();
        $roles = KObjectConfig::unbox($data->roles);

        if(empty($roles)) {
            $this->getSession()->set('user.roles', JAccess::getAuthorisedViewLevels($this->getId()));
        }

        return parent::getRoles();
    }

    /**
     * Returns the groups the user is part of
     *
     * @return array An array of group id's
     */
    public function getGroups()
    {
        $data  = $this->getData();
        $groups = KObjectConfig::unbox($data->groups);

        if(empty($groups)) {
            $this->getSession()->set('user.groups', JAccess::getGroupsByUser($this->getId()));
        }

        return parent::getGroups();
    }

    /**
     * Method to check object authorisation against an access control object and optionally an access extension object
     *
     * @param   string  $action     The name of the action to check for permission.
     * @param   string  $assetname  The name of the asset on which to perform the action.
     * @return  boolean  True if authorised
     */
    public function authorise($action, $assetname = null)
    {
        return JFactory::getUser()->authorise($action, $assetname);
    }
}
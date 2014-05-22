<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright	Copyright (C) 2007 - 2014 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/koowa for the canonical source repository
 */

/**
 * User
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Koowa\Component\Koowa
 */
class ComKoowaUser extends KUser implements ComKoowaUserInterface
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
                'roles'      => $user->getAuthorisedViewLevels(),
                'groups'     => $user->getAuthorisedGroups(),
                'password'   => $user->password,
                'salt'       => '',
                'authentic'  => !$user->guest,
                'enabled'    => !$user->block,
                'expired'    => !$user->activation,
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
<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright	Copyright (C) 2007 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
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
                'attributes' => $user->getParameters()->toArray(),
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
}
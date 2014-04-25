<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright	Copyright (C) 2007 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/koowa for the canonical source repository
 */

/**
 * User Provider
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Koowa\Component\Koowa
 */
class ComKoowaUserProvider extends KUserProvider
{
    /**
     * Loads the user for the given username or user id
     *
     * @param string $identifier A unique user identifier, (i.e a username or user id)
     * @param bool  $refresh     If TRUE and the user has already been loaded it will be re-loaded.
     * @return KUserInterface|null Returns a UserInterface object or NULL if the user could not be found.
     */
    public function load($identifier, $refresh = false)
    {
        // Find the user id
        if (!is_numeric($identifier))
        {
            if(!$identifier = JUserHelper::getUserId($identifier)) {
                $identifier = 0;
            }
        }

        // Fetch the user
        $user = $this->getObject('user');
        if ($user->getId() != $identifier)
        {
            $user = parent::load($identifier, $refresh);

            if (empty($user))
            {
                $user = $this->create(array(
                    'id'   => $identifier,
                    'name' => $this->getObject('translator')->translate('Anonymous')
                ));
            }
        }

        return $user;
    }

    /**
     * Fetch the user for the given user identifier from the backend
     *
     * @param string $identifier A unique user identifier, (i.e a username or email address)
     * @return KUserInterface|null Returns a UserInterface object or NULL if the user could not be found.
     */
    public function fetch($identifier)
    {
        //Load the user
        $user = JFactory::getUser($identifier);

        if($user->id)
        {
            $data = array(
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
            );

           $user = $this->create($data);
        }
        else $user = null;

        return $user;
    }
}
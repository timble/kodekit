<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright	Copyright (C) 2007 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/koowa for the canonical source repository
 */

/**
 * Abstract Controller Permission
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Koowa\Library\Controller
 */
abstract class KControllerPermissionAbstract extends KObjectMixinAbstract implements KControllerPermissionInterface
{
    /**
     * Permission handler for render actions
     *
     * @return  boolean  Return TRUE if action is permitted. FALSE otherwise.
     */
    public function canRender()
    {
        return ($this->getMixer() instanceof KControllerViewable);
    }

    /**
     * Permission handler for read actions
     *
     * Method returns TRUE iff the controller implements the KControllerModellable interface.
     *
     * @return  boolean Return TRUE if action is permitted. FALSE otherwise.
     */
    public function canRead()
    {
        return ($this->getMixer() instanceof KControllerModellable);
    }

    /**
     * Permission handler for browse actions
     *
     * Method returns TRUE iff the controller implements the KControllerModellable interface.
     *
     * @return  boolean  Return TRUE if action is permitted. FALSE otherwise.
     */
    public function canBrowse()
    {
        return ($this->getMixer() instanceof KControllerModellable);
    }

    /**
     * Permission handler for add actions
     *
     * Method returns TRUE iff the controller implements the KControllerModellable interface and the user is authentic
     * and the account is enabled.
     *
     * @return  boolean  Return TRUE if action is permitted. FALSE otherwise.
     */
    public function canAdd()
    {
        if($this->getMixer() instanceof KControllerModellable)
        {
            $user = $this->getUser();
            if ($user->isAuthentic() && $user->isEnabled()) {
                return true;
            }
        }

        return false;
    }

    /**
     * Permission handler for edit actions
     *
     * Method returns TRUE iff the controller implements the KControllerModellable interface and the user is authentic
     * and the account is enabled.
     *
     * @return  boolean  Return TRUE if action is permitted. FALSE otherwise.
     */
    public function canEdit()
    {
        if($this->getMixer() instanceof KControllerModellable)
        {
            $user = $this->getUser();
            if ($user->isAuthentic() && $user->isEnabled()) {
                return true;
            }
        }

        return false;
    }

    /**
     * Permission handler for delete actions
     *
     * Method returns true of the controller implements KControllerModellable interface and the user is authentic.
     *
     * @return  boolean  Returns TRUE if action is permitted. FALSE otherwise.
     */
    public function canDelete()
    {
        if($this->getMixer() instanceof KControllerModellable)
        {
            $user = $this->getUser();
            if ($user->isAuthentic() && $user->isEnabled()) {
                return true;
            }
        }

        return false;
    }
}
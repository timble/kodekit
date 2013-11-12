<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright	Copyright (C) 2007 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/koowa for the canonical source repository
 */

/**
 * Permissible Controller Behavior
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Koowa\Library\Controller
 */
class ComKoowaControllerBehaviorPermissible extends KControllerBehaviorPermissible
{
    /**
     * Command handler
     *
     * Handles token validation
     *
     * {@inheritdoc}
     */
    public function canExecute($action)
    {
        if (!$this->_checkToken()) {
            throw new KControllerExceptionForbidden('Invalid token or session time-out');
        }

        return parent::canExecute($action);
    }

    /**
     * Check the token to prevent CSRF exploits
     *
     * @return  boolean Returns FALSE if the check failed. Otherwise TRUE.
     */
    protected function _checkToken()
    {
        if ($this->isDispatched())
        {
            $method = KRequest::method();

            //Only check the token for PUT, DELETE and POST requests
            if ($method != KHttpRequest::GET && $method != KHttpRequest::OPTIONS)
            {
                if (KRequest::token() !== JSession::getFormToken()) {
                    return false;
                }
            }
        }

        return true;
    }
}
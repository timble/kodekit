<?php
/**
 * Nooku Framework - http://nooku.org/framework
 *
 * @copyright   Copyright (C) 2007 - 2014 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/nooku/nooku-framework for the canonical source repository
 */

/**
 * User Interface
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Koowa\Component\Koowa\User
 */
interface ComKoowaUserInterface extends KUserInterface
{
    /**
     * Returns the username of the user
     *
     * @return string The username
     */
    public function getUsername();

    /**
     * Method to get a parameter value
     *
     * @param   string  $key      Parameter key
     * @param   mixed   $default  Parameter default value
     *
     * @return  mixed  The value or the default if it did not exist
     */
    public function getParameter($key, $default = null);

    /**
     * Method to check user authorisation
     *
     * @param   string  $action     The name of the action to check for permission.
     * @param   string  $assetname  The name of the asset on which to perform the action.
     *
     * @return  boolean  True if authorised
     */
    public function authorise($action, $assetname = null);
}
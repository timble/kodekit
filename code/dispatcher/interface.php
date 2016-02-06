<?php
/**
 * Nooku Framework - http://nooku.org/framework
 *
 * @copyright   Copyright (C) 2007 - 2014 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/nooku/nooku-framework for the canonical source repository
 */

/**
 * Dispatcher Interface
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Koowa\Library\Dispatcher
 */
interface KDispatcherInterface extends KControllerInterface
{
    /**
     * Method to get a controller object
     *
     * @return  KControllerAbstract
     */
    public function getController();

    /**
     * Method to set a controller object attached to the dispatcher
     *
     * @param   mixed   $controller An object that implements ControllerInterface, ObjectIdentifier object
     *                              or valid identifier string
     * @param  array  $config  An optional associative array of configuration options
     * @return	KDispatcherInterface
     */
    public function setController($controller, $config = array());

    /**
     * Method to get a controller action to be executed
     *
     * @return	string
     */
    public function getControllerAction();

    /**
     * Method to set the controller action to be executed
     *
     * @return	KDispatcherInterface
     */
    public function setControllerAction($action);
}

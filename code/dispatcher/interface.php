<?php
/**
 * Kodekit - http://timble.net/kodekit
 *
 * @copyright   Copyright (C) 2007 - 2016 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/timble/kodekit for the canonical source repository
 */

namespace Kodekit\Library;

/**
 * Dispatcher Interface
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Kodekit\Library\Dispatcher
 */
interface DispatcherInterface extends ControllerInterface
{
    /**
     * Method to get a controller object
     *
     * @return  ControllerAbstract
     */
    public function getController();

    /**
     * Method to set a controller object attached to the dispatcher
     *
     * @param   mixed   $controller An object that implements ControllerInterface, ObjectIdentifier object
     *                              or valid identifier string
     * @param  array  $config  An optional associative array of configuration options
     * @return	DispatcherInterface
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
     * @return	DispatcherInterface
     */
    public function setControllerAction($action);
}

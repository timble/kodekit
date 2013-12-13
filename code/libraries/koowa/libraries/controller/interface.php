<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright	Copyright (C) 2007 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/koowa for the canonical source repository
 */

/**
 * Controller Interface
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Koowa\Library\Controller
 */
interface KControllerInterface
{
    /**
     * Execute an action by triggering a method in the derived class.
     *
     * @param   string             $action  The action to execute
     * @param   KControllerContextInterface $context A command context object
     * @throws  BadMethodCallException
     * @return  mixed|bool      The value returned by the called method, false in error case.
     */
    public function execute($action, KControllerContextInterface $context);

    /**
     * Get the controller context
     *
     * @return  KControllerContextInterface
     */
    public function getContext();

    /**
     * Gets the available actions in the controller.
     *
     * @return array Actions
     */
    public function getActions();

    /**
     * Set the request object
     *
     * @param KControllerRequestInterface $request A request object
     * @return KControllerAbstract
     */
    public function setRequest(KControllerRequestInterface $request);

    /**
     * Get the request object
     *
     * @throws UnexpectedValueException	If the request doesn't implement the KControllerRequestInterface
     * @return KControllerRequestInterface
     */
    public function getRequest();

    /**
     * Set the response object
     *
     * @param KControllerResponseInterface $response A response object
     * @return KControllerAbstract
     */
    public function setResponse(KControllerResponseInterface $response);

    /**
     * Get the response object
     *
     * @throws	UnexpectedValueException	If the response doesn't implement the KControllerResponseInterface
     * @return KControllerResponseInterface
     */
    public function getResponse();

    /**
     * Set the user object
     *
     * @param KControllerUserInterface $user A request object
     * @return KControllerUser
     */
    public function setUser(KControllerUserInterface $user);

    /**
     * Get the user object
     *
     * @return KControllerUserInterface
     */
    public function getUser();

    /**
     * Has the controller been dispatched
     *
     * @return  boolean	Returns true if the controller has been dispatched
     */
    public function isDispatched();
}

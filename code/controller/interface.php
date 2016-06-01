<?php
/**
 * Kodekit - http://timble.net/kodekit
 *
 * @copyright   Copyright (C) 2007 - 2016 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license     MPL v2.0 <https://www.mozilla.org/en-US/MPL/2.0>
 * @link        https://github.com/timble/kodekit for the canonical source repository
 */

namespace Kodekit\Library;

/**
 * Controller Interface
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Kodekit\Library\Controller
 */
interface ControllerInterface
{
    /**
     * Execute an action by triggering a method in the derived class.
     *
     * @param   string             $action  The action to execute
     * @param   ControllerContext $context A command context object
     * @throws  \BadMethodCallException
     * @return  mixed|bool      The value returned by the called method, false in error case.
     */
    public function execute($action, ControllerContext $context);

    /**
     * Get the controller context
     *
     * @param   ControllerContextInterface $context Context to cast to a local context
     * @return  ControllerContext
     */
    public function getContext(ControllerContextInterface $context = null);

    /**
     * Gets the available actions in the controller.
     *
     * @return array Actions
     */
    public function getActions();

    /**
     * Set the request object
     *
     * @param ControllerRequestInterface $request A request object
     * @return ControllerAbstract
     */
    public function setRequest(ControllerRequestInterface $request);

    /**
     * Get the request object
     *
     * @throws \UnexpectedValueException	If the request doesn't implement the ControllerRequestInterface
     * @return ControllerRequestInterface
     */
    public function getRequest();

    /**
     * Set the response object
     *
     * @param ControllerResponseInterface $response A response object
     * @return ControllerAbstract
     */
    public function setResponse(ControllerResponseInterface $response);

    /**
     * Get the response object
     *
     * @throws	\UnexpectedValueException	If the response doesn't implement the ControllerResponseInterface
     * @return ControllerResponseInterface
     */
    public function getResponse();

    /**
     * Set the user object
     *
     * @param UserInterface $user A request object
     * @return UserInterface
     */
    public function setUser(UserInterface $user);

    /**
     * Get the user object
     *
     * @return UserInterface
     */
    public function getUser();

    /**
     * Has the controller been dispatched
     *
     * @return  boolean	Returns true if the controller has been dispatched
     */
    public function isDispatched();
}

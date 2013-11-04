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
     * Gets the available actions in the controller.
     *
     * @return array Actions
     */
    public function getActions();

    /**
     * Register (map) an action to a method in the class.
     *
     * @param   string  $alias  The action.
     * @param   string  $action The name of the method in the derived class to perform for this action.
     * @return  KControllerInterface
     */
    public function registerActionAlias( $alias, $action );

	/**
	 * Get the request information
	 *
     * @return KControllerRequestInterface	An object with request information
	 */
	public function getRequest();

	/**
	 * Set the request information
	 *
	 * @param array	$request An associative array of request information
	 * @return KControllerInterface
	 */
	public function setRequest(array $request);

    /**
     * Has the controller been dispatched
     *
     * @return  boolean	Returns true if the controller has been dispatched
     */
    public function isDispatched();
}

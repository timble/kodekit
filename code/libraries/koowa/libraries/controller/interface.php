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
     * @param   string          $action  The action to execute
     * @param   KCommandContext $context A command context object
     * @throws  BadMethodCallException
     * @return  mixed|bool      The value returned by the called method, false in error case.
     */
    public function execute($action, KCommandContext $context);

    /**
     * Gets the available actions in the controller.
     *
     * @return  array Array[i] of action names.
     */
    public function getActions($reload = false);

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
	 * @return KConfig A KConfig object with request information
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
     * Check if a behavior exists
     *
     * @param 	string	$behavior The name of the behavior
     * @return  boolean	TRUE if the behavior exists, FALSE otherwise
     */
    public function hasBehavior($behavior);

    /**
     * Add one or more behaviors to the controller
     *
     * @param   array   $behaviors Array of one or more behaviors to add.
     * @return  KControllerAbstract
     */
    public function addBehavior($behaviors);

    /**
     * Get a behavior by identifier
     *
     * @param  string        $behavior The name of the behavior
     * @param  KConfig|array $config Configuration of the behavior
     * @throws UnexpectedValueException
     * @return KControllerBehaviorAbstract
     */
    public function getBehavior($behavior, $config = array());

    /**
     * Gets the behaviors of the table
     *
     * @return array An asscociate array of table behaviors, keys are the behavior names
     */
    public function getBehaviors();

    /**
     * Has the controller been dispatched
     *
     * @return  boolean	Returns true if the controller has been dispatched
     */
    public function isDispatched();
}

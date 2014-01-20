<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright	Copyright (C) 2007 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/koowa for the canonical source repository
 */

/**
 * Command Invoker Interface
 *
 * @author  Johan Janssens <http://github.com/johanjanssens>
 * @package Koowa\Library\Command
 */
interface KCommandInvokerInterface extends KObjectHandlable
{
    /**
     * Priority levels
     */
    const PRIORITY_HIGHEST = 1;
    const PRIORITY_HIGH    = 2;
    const PRIORITY_NORMAL  = 3;
    const PRIORITY_LOW     = 4;
    const PRIORITY_LOWEST  = 5;

    /**
     * Command handler
     *
     * @param KCommandInterface $command    The command
     * @param  mixed            $condition  The break condition
     * @return array|mixed Returns an array of the handler results in FIFO order. If a handler breaks and the break
     *                     condition is not NULL returns the break condition.
     */
	public function executeCommand(KCommandInterface $command, $condition = null);

    /**
     * Add a command handler
     *
     * If the handler has already been added. It will not be re-added but parameters will be merged. This allows to
     * change or add parameters for existing handlers.
     *
     * @param  	string          $command  The command name to register the handler for
     * @param 	string|Closure  $method   The name of the method or a Closure object
     * @param   array|object    $params   An associative array of config parameters or a KObjectConfig object
     * @throws  InvalidArgumentException If the callback is not a callable
     * @return  KCommandInvokerAbstract
     */
    public function addCommandHandler($command, $method, $params = array());

    /**
     * Remove a command handler
     *
     * @param  	string	$command  The command to unregister the handler from
     * @param 	string	$method   The name of the method to unregister
     * @return  KCommandInvokerAbstract
     */
    public function removeCommandHandler($command, $method);

    /**
     * Get the handlers for a command
     *
     * @param string $command   The command
     * @return  array An array of command handlers
     */
    public function getCommandHandlers($command);

	/**
	 * Get the priority of the invoker
	 *
	 * @return	integer The invoker priority
	 */
  	public function getPriority();
}

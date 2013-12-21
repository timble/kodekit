<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright	Copyright (C) 2007 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/koowa for the canonical source repository
 */

/**
 * Command Chain
 *
 * The command queue implements a double linked list. The command handle is used as the key. Each command can have a
 * priority, default priority is 3 The queue is ordered by priority, commands with a higher priority are called first.
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Koowa\Library\Command
 */
interface KCommandChainInterface
{
    /**
     * Break conditions
     */
    const CONDITION_FALSE     = false; //Stop when first invoker indicates that it has failed
    const CONDITION_TRUE      = true;  //Stop when first invoker indicates that it has succeeded
    const CONDITION_EXCEPTION = -1;    //Break when an invoker throws an CommandExceptionInvokerFailed exception

    /**
     * Run the commands in the chain
     *
     * If a command returns the 'break condition' the executing is halted. If no break condition is specified the
     * command chain will pass the command invokers, regardless of the invoker result returned.
     *
     * @param   string  $name
     * @param   KCommandInterface $command
     * @param   mixed   $condition The break condition
     * @return  void|mixed If the chain breaks, returns the break condition. If the chain is not enabled will void
     */
    public function run($name, KCommandInterface $command, $condition = null);
    /**
     * Attach a command to the chain
     *
     * The priority parameter can be used to override the command priority while enqueueing the command.
     *
     * @param   KCommandInvokerInterface|KObjectHandlable   $invoker A command invoker
     * @param   integer            $priority The command priority, usually between 1 (high priority) and 5 (lowest),
     *                                        default is 3. If no priority is set, the command priority will be used
     *                                        instead.
     * @return KCommandChainInterface
     * @throws InvalidArgumentException if the object does not implement KCommandInvokerInterface
     */
    public function enqueue(KObjectHandlable $invoker, $priority = null);

    /**
     * Removes a command from the queue
     *
     * @param   KCommandInvokerInterface|KObjectHandlable   $invoker A command invoker
     * @return  boolean    TRUE on success FALSE on failure
     * @throws  \InvalidArgumentException if the object does not implement KCommandInvokerInterface
     */
    public function dequeue(KObjectHandlable $invoker);

    /**
     * Check if the queue does contain a given object
     *
     * @param   KCommandInvokerInterface|KObjectHandlable   $invoker A command invoker
     * @return bool
     * @throws  InvalidArgumentException if the object does not implement KCommandInvokerInterface
     */
    public function contains(KObjectHandlable $invoker);

    /**
     * Enable the chain
     *
     * @return  $this
     */
    public function enable();

    /**
     * Disable the chain
     *
     * If the chain is disabled running the chain will always return TRUE
     *
     * @return  $this
     */
    public function disable();

    /**
     * Set the priority of a command
     *
     * @param   KCommandInvokerInterface|KObjectHandlable   $invoker A command invoker
     * @param integer           $priority
     * @return KCommandChainInterface
     * @throws InvalidArgumentException if the object doesn't implement KCommandInvokerInterface
     */
    public function setPriority(KObjectHandlable $invoker, $priority);

    /**
     * Get the priority of a command
     *
     * @param   KCommandInvokerInterface|KObjectHandlable   $invoker A command invoker
     * @return integer The command priority
     * @throws InvalidArgumentException if the object doesn't implement KCommandInvokerInterface
     */
    public function getPriority(KObjectHandlable $invoker);

    /**
     * Check of the command chain is enabled
     *
     * @return bool
     */
    public function isEnabled();
}

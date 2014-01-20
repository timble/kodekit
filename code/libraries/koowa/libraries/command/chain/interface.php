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
 * The command chain implements a queue. The command handle is used as the key. Each command can have a priority, default
 * priority is 3 The queue is ordered by priority, commands with a higher priority are called first.
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Koowa\Library\Command
 */
interface KCommandChainInterface
{
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
     * Invoke a command by calling all registered invokers
     *
     * If a command invoker returns the 'break condition' the executing is halted. If no break condition is specified the
     * the command chain will execute all command invokers, regardless of the invoker result returned.
     *
     * @param string|KCommandInterface  $command    The command name or a KCommandInterface object
     * @param array|Traversable         $attributes An associative array or a Traversable object
     * @param KObjectInterface          $subject    The command subject
     * @return array|mixed Returns an array of the command results in FIFO order where the key holds the invoker identifier
     *                     and the value the result returned by the invoker. If the chain breaks, and the break condition
     *                     is not NULL returns the break condition instead.
     */
    public function invokeCommand($command, $attributes = array(), $subject = null);

    /**
     * Attach a command to the chain
     *
     * @param KCommandInvokerInterface  $invoker  The command invoker
     * @return KCommandChainInterface
     */
    public function addInvoker(KCommandInvokerInterface $invoker);

    /**
     * Get the list of invokers enqueue in the chain
     *
     * @return  KObjectQueue   An object queue containing the invokers
     */
    public function getInvokers();

    /**
     * Set the priority of a command invoker
     *
     * @param KCommandInvokerInterface $invoker   A command invoker
     * @param integer                   $priority  The command priority
     * @return KCommandChainInterface
     */
    public function setInvokerPriority(KCommandInvokerInterface $invoker, $priority);

    /**
     * Get the priority of a command invoker
     *
     * @param  KCommandInvokerInterface $invoker A command invoker
     * @return integer The command priority
     */
    public function getInvokerPriority(KCommandInvokerInterface $invoker);

    /**
     * Check of the command chain is enabled
     *
     * @return bool
     */
    public function isEnabled();
}

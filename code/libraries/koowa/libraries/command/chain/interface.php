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
     * Attach a command to the chain
     *
     * The priority parameter can be used to override the command priority while enqueueing the command.
     *
     * @param   KObjectHandlable   $command
     * @param   integer            $priority The command priority, usually between 1 (high priority) and 5 (lowest),
     *                                        default is 3. If no priority is set, the command priority will be used
     *                                        instead.
     * @return KCommandChain
     * @throws \InvalidArgumentException if the object does not implement KCommandInterface
     */
    public function enqueue(KObjectHandlable $command, $priority = null);

    /**
     * Removes a command from the queue
     *
     * @param   KObjectHandlable $command
     * @return  boolean    TRUE on success FALSE on failure
     * @throws  \InvalidArgumentException if the object does not implement KCommandInterface
     */
    public function dequeue(KObjectHandlable $command);

    /**
     * Check if the queue does contain a given object
     *
     * @param  KObjectHandlable $command
     * @return bool
     * @throws  \InvalidArgumentException if the object does not implement KCommandInterface
     */
    public function contains(KObjectHandlable $command);

    /**
     * Run the commands in the chain
     *
     * If a command returns the 'break condition' the executing is halted.
     *
     * @param   string          $name
     * @param   KCommandContext $context
     * @return  void|boolean    If the chain breaks, returns the break condition. Default returns void.
     */
    public function run($name, KCommandContext $context);

    /**
     * Enable the chain
     *
     * @return  KCommandChain
     */
    public function enable();

    /**
     * Disable the chain
     *
     * If the chain is disabled running the chain will always return TRUE
     *
     * @return  KCommandChain
     */
    public function disable();

    /**
     * Set the priority of a command
     *
     * @param KObjectHandlable  $command
     * @param integer           $priority
     * @return KCommandChain
     * @throws \InvalidArgumentException if the object doesn't implement KCommandInterface
     */
    public function setPriority(KObjectHandlable $command, $priority);

    /**
     * Get the priority of a command
     *
     * @param  KObjectHandlable $command
     * @return integer The command priority
     * @throws \InvalidArgumentException if the object doesn't implement KCommandInterface
     */
    public function getPriority(KObjectHandlable $command);

    /**
     * Factory method for a command context.
     *
     * @return  KCommandContext
     */
    public function getContext();

    /**
     * Get the chain object stack
     *
     * @return     KObjectStack
     */
    public function getStack();
}

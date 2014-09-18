<?php
/**
 * Nooku Framework - http://nooku.org/framework
 *
 * @copyright   Copyright (C) 2007 - 2014 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/nooku/nooku-framework for the canonical source repository
 */

/**
 * Command Chain
 *
 * The command chain implements a queue. The command handle is used as the key. Each command can have a priority, default
 * priority is 3 The queue is ordered by priority, commands with a higher priority are called first.
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Koowa\Library\Command\Chain
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
     * Execute a command by executing all registered handlers
     *
     * If a command handler returns the 'break condition' the executing is halted. If no break condition is specified the
     * the command chain will execute all command handlers, regardless of the handler result returned.
     *
     * @param string|KCommandInterface  $command    The command name or a KCommandInterface object
     * @param array|Traversable         $attributes An associative array or a Traversable object
     * @param KObjectInterface          $subject    The command subject
     * @return mixed|null If a handlers breaks, returns the break condition. NULL otherwise.
     */
    public function execute($command, $attributes = array(), $subject = null);

    /**
     * Attach a command to the chain
     *
     * @param KCommandHandlerInterface  $handler  The command handler
     * @return KCommandChainInterface
     */
    public function addHandler(KCommandHandlerInterface $handler);

    /**
     * Removes a command from the chain
     *
     * @param  KCommandHandlerInterface  $handler  The command handler
     * @return  KCommandChain
     */
    public function removeHandler(KCommandHandlerInterface $handler);

    /**
     * Get the list of handler enqueue in the chain
     *
     * @return  KObjectQueue   An object queue containing the handlers
     */
    public function getHandlers();

    /**
     * Set the priority of a command handler
     *
     * @param KCommandHandlerInterface $handler   A command handler
     * @param integer                   $priority  The command priority
     * @return KCommandChainInterface
     */
    public function setHandlerPriority(KCommandHandlerInterface $handler, $priority);

    /**
     * Get the priority of a command handlers
     *
     * @param  KCommandHandlerInterface $handler A command handler
     * @return integer The command priority
     */
    public function getHandlerPriority(KCommandHandlerInterface $handler);

    /**
     * Set the break condition
     *
     * @param mixed|null $condition The break condition, or NULL to set reset the break condition
     * @return KCommandChainInterface
     */
    public function setBreakCondition($condition);

    /**
     * Get the break condition
     *
     * @return mixed|null   Returns the break condition, or NULL if not break condition is set.
     */
    public function getBreakCondition();

    /**
     * Check of the command chain is enabled
     *
     * @return bool
     */
    public function isEnabled();
}

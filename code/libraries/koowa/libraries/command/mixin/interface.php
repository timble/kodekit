<?php
/**
 * Nooku Framework - http://nooku.org/framework
 *
 * @copyright   Copyright (C) 2007 - 2014 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/nooku/nooku-framework for the canonical source repository
 */

/**
 * Command Mixin Interface
 *
 * Mixin can be mixed into objects that want to implement a chain of responsibility or chain of command pattern.
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Koowa\Library\Command\Mixin
 */
interface KCommandMixinInterface
{
    /**
     * Invoke a command by calling all registered handlers
     *
     * If a command handler returns the 'break condition' the executing is halted. If no break condition is specified the
     * the command chain will execute all command handlers, regardless of the handler result returned.
     *
     * @param  string|KCommandInterface  $command    The command name or a KCommandInterface object
     * @param  array|Traversable         $attributes An associative array or a Traversable object
     * @param  KObjectInterface          $subject    The command subject
     * @return array|mixed Returns an array of the command results in FIFO order where the key holds the handler identifier
     *                     and the value the result returned by the handler. If the chain breaks, and the break condition
     *                     is not NULL returns the break condition instead.
     */
    public function invokeCommand($command, $attributes = null, $subject = null);

    /**
     * Get the chain of command object
     *
     * @throws UnexpectedValueException
     * @return  KCommandChainInterface
     */
    public function getCommandChain();

    /**
     * Set the chain of command object
     *
     * @param KCommandChainInterface $chain A command chain object
     * @return KObjectInterface The mixer object
     */
    public function setCommandChain(KCommandChainInterface $chain);

    /**
     * Attach a command handler to the chain
     *
     * @param  mixed $handler An object that implements KCommandHandlerInterface, an KObjectIdentifier
     *                        or valid identifier string
     * @param  array  $config   An optional associative array of configuration options
     * @return KObjectInterface The mixer object
     */
    public function addCommandHandler($handler, $config = array());

    /**
     * Removes a command handler from the chain
     *
     * @param KCommandHandlerInterface  $handler  The command handler
     * @return KObjectInterface The mixer object
     */
    public function removeCommandHandler(KCommandHandlerInterface $handler);

    /**
     * Gets the command handlers
     *
     * @return array An array of command handlers
     */
    public function getCommandHandlers();
}

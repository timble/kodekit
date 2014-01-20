<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright	Copyright (C) 2007 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/koowa for the canonical source repository
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
     * Invoke a command by calling all registered invokers
     *
     * If a command invoker returns the 'break condition' the executing is halted. If no break condition is specified the
     * the command chain will execute all command invokers, regardless of the invoker result returned.
     *
     * @param  string|KCommandInterface  $command    The command name or a KCommandInterface object
     * @param  array|Traversable         $attributes An associative array or a Traversable object
     * @param  KObjectInterface          $subject    The command subject
     * @return array|mixed Returns an array of the command results in FIFO order where the key holds the invoker identifier
     *                     and the value the result returned by the invoker. If the chain breaks, and the break condition
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
     * Attach a command to the chain
     *
     * @param  mixed $invoker An object that implements KCommandInvokerInterface, an KObjectIdentifier
     *                        or valid identifier string
     * @param  array  $config   An optional associative array of configuration options
     * @return KObjectInterface The mixer object
     */
    public function addCommandInvoker($invoker, $config = array());

    /**
     * Removes a command from the chain
     *
     * @param KCommandInvokerInterface  $invoker  The command invoker
     * @return KObjectInterface The mixer object
     */
    public function removeCommandInvoker(KCommandInvokerInterface $invoker);

    /**
     * Get a command invoker by identifier
     *
     * @param  mixed $invoker An object that implements ObjectInterface, ObjectIdentifier object
     *                        or valid identifier string
     * @param  array  $config An optional associative array of configuration settings
     * @throws UnexpectedValueException    If the invoker is not implementing the KCommandInvokerInterface
     * @return KCommandInvokerInterface
     */
    public function getCommandInvoker($invoker, $config = array());

    /**
     * Gets the command invokers
     *
     * @return array An array of command invokers
     */
    public function getCommandInvokers();
}

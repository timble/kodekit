<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright	Copyright (C) 2007 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/koowa for the canonical source repository
 */

/**
 * Command Chain Mixin
 *
 * Class can be used as a mixin in classes that want to implement a chain of responsibility or chain of command pattern.
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Koowa\Library\Command\Mixin
 */
class KCommandMixin extends KObjectMixinAbstract
{
    /**
     * Chain of command object
     *
     * @var KCommandChain
     */
    protected $_command_chain;

    /**
     * Object constructor
     *
     * @param   KObjectConfig $config Configuration options
     * @throws InvalidArgumentException
     */
    public function __construct(KObjectConfig $config)
    {
        parent::__construct($config);

        if(is_null($config->command_chain)) {
            throw new InvalidArgumentException('command_chain [KCommandChainInterface] config option is required');
        }

        //Create a command chain object
        $this->_command_chain = $config->command_chain;

        //Enqueue the callback command
        if($config->enable_callbacks)
        {
            $command = $this->getMixer()->mixin('koowa:command.invoker.callback', $config);
            $this->getCommandChain()->enqueue($command, $config->callback_priority);
        }

        //Enqueue the event command
        if($config->dispatch_events)
        {
            $command = $this->getMixer()->mixin('koowa:command.invoker.event', $config);
            $this->getCommandChain()->enqueue($command, $config->event_priority);
        }
    }

    /**
     * Initializes the options for the object
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param   KObjectConfig $config Configuration options
     * @return  void
     */
    protected function _initialize(KObjectConfig $config)
    {
        $config->append(array(
            'command_chain'     => 'koowa:command.chain',
            'event_dispatcher'  => null,
            'dispatch_events'   => true,
            'event_priority'    => KCommandInvokerInterface::PRIORITY_LOWEST,
            'enable_callbacks'  => false,
            'callback_priority' => KCommandInvokerInterface::PRIORITY_HIGH,
        ));

        parent::_initialize($config);
    }

    /**
     * Get the command chain context
     *
     * This functions inserts a 'caller' variable in the context which contains the mixer object.
     *
     * @return  KCommand
     */
    public function getContext()
    {
        $context = $this->getCommandChain()->getContext();
        $context->setSubject($this->getMixer());

        return $context;
    }

    /**
     * Get the chain of command object
     *
     * @throws UnexpectedValueException
     * @return  KCommandChainInterface
     */
    public function getCommandChain()
    {
        if(!$this->_command_chain instanceof KCommandChainInterface)
        {
            $this->_command_chain = $this->getObject($this->_command_chain);

            if(!$this->_command_chain instanceof KCommandChainInterface)
            {
                throw new UnexpectedValueException(
                    'CommandChain: '.get_class($this->_command_chain).' does not implement KCommandChainInterface'
                );
            }
        }

        return $this->_command_chain;
    }

    /**
     * Set the chain of command object
     *
     * @param   KCommandChainInterface $chain A command chain object
     * @return  KObject The mixer object
     */
    public function setCommandChain(KCommandChainInterface $chain)
    {
        $this->_command_chain = $chain;
        return $this->getMixer();
    }

	/**
     * Preform a deep clone of the object.
     *
     * @return void
     */
    public function __clone()
    {
        $this->_command_chain = clone $this->_command_chain;
    }
}

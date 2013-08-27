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
     */
    public function __construct(KObjectConfig $config)
    {
        parent::__construct($config);

        //Create a command chain object
        $this->_command_chain = $config->command_chain;

        //Mixin the callback mixer if callbacks have been enabled
        if($config->enable_callbacks)
        {
            $this->getMixer()->mixin(new KCommandCallback(new KObjectConfig(array(
                'mixer'           => $this->getMixer(),
                'priority'        => $config->callback_priority,
                'command_chain'   => $this->_command_chain,
            ))));
        }

        //Enqueue the event command with a lowest priority to make sure it runs last
        if($config->dispatch_events) {
            $this->_command_chain->enqueue($config->event, $config->event_priority);
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
            'command_chain'     => KObjectManager::getInstance()->getObject('koowa:command.chain'),
            'event'				=> KObjectManager::getInstance()->getObject('koowa:command.event'),
            'dispatch_events'   => true,
            'event_priority'    => KCommand::PRIORITY_LOWEST,
            'enable_callbacks'  => false,
            'callback_priority' => KCommand::PRIORITY_HIGH,
        ));

        parent::_initialize($config);
    }

    /**
     * Get the command chain context
     *
     * This functions inserts a 'caller' variable in the context which contains the mixer object.
     *
     * @return  KCommandContext
     */
    public function getCommandContext()
    {
        $context = $this->_command_chain->getContext();
        $context->caller = $this->getMixer();

        return $context;
    }

    /**
     * Get the chain of command object
     *
     * @return  KCommandChainInterface
     */
    public function getCommandChain()
    {
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

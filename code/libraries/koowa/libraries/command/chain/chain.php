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
class KCommandChain extends KObject implements KCommandChainInterface
{
    /**
     * The chain stack
     *
     * Used to track recursive chain nesting.
     *
     * @var KObjectStack
     */
    private $__stack;

    /**
     * The handler queue
     *
     * @var KObjectQueue
     */
    private $__queue;

    /**
     * Enabled status of the chain
     *
     * @var boolean
     */
    private $__enabled;

    /**
     * The chain break condition
     *
     * @var boolean
     */
    protected $_break_condition;

    /**
     * Constructor
     *
     * @param KObjectConfig  $config  An optional KObjectConfig object with configuration options
     * @return KCommandChain
     */
    public function __construct(KObjectConfig $config)
    {
        parent::__construct($config);

        //Set the chain enabled state
        $this->__enabled = (boolean) $config->enabled;

        //Set the chain break condition
        $this->_break_condition = $config->break_condition;

        $this->__stack = $this->getObject('lib:object.stack');
        $this->__queue = $this->getObject('lib:object.queue');
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
            'break_condition' => false,
            'enabled'         => true
        ));

        parent::_initialize($config);
    }

    /**
     * Enable the chain
     *
     * @return  $this
     */
    public function enable()
    {
        $this->__enabled = true;
        return $this;
    }

    /**
     * Disable the chain
     *
     * If the chain is disabled running the chain will always return TRUE
     *
     * @return  $this
     */
    public function disable()
    {
        $this->__enabled = false;
        return $this;
    }

    /**
     * Execute a command by executing all registered handlers
     *
     * If a command handler returns the 'break condition' the executing is halted. If no break condition is specified the
     * the command chain will execute all command handlers, regardless of the handler result returned.
     *
     * @param  string|KCommandInterface  $command    The command name or a KCommandInterface object
     * @param  array|Traversable         $attributes An associative array or a Traversable object
     * @param  KObjectInterface          $subject    The command subject
     * @return mixed|null If a handler breaks, returns the break condition. NULL otherwise.
     */
    public function execute($command, $attributes = null, $subject = null)
    {
        $result = null;

        if ($this->isEnabled())
        {
            $this->__stack->push(clone $this->__queue);

            //Make sure we have an command object
            if (!$command instanceof KCommandInterface)
            {
                if($attributes instanceof KCommandInterface)
                {
                    $name    = $command;
                    $command = $attributes;

                    $command->setName($name);
                }
                else $command = new KCommand($command, $attributes, $subject);
            }

            foreach ($this->__stack->peek() as $handler)
            {
                $result = $handler->execute($command, $this);

                if($result === $this->getBreakCondition()) {
                    break;
                }
            }

            $this->__stack->pop();
        }

        return $result;
    }

    /**
     * Attach a command to the chain
     *
     * @param   KCommandHandlerInterface  $handler  The command handler
     * @return KCommandChain
     */
    public function addHandler(KCommandHandlerInterface $handler)
    {
        $this->__queue->enqueue($handler, $handler->getPriority());
        return $this;
    }

    /**
     * Removes a command from the chain
     *
     * @param  KCommandHandlerInterface  $handler  The command handler
     * @return  KCommandChain
     */
    public function removeHandler(KCommandHandlerInterface $handler)
    {
        $this->__queue->dequeue($handler);
        return $this;
    }

    /**
     * Get the list of handlers enqueue in the chain
     *
     * @return  KObjectQueue   An object queue containing the handlers
     */
    public function getHandlers()
    {
        return $this->__queue;
    }

    /**
     * Set the priority of a command
     *
     * @param  KCommandHandlerInterface $handler   A command handler
     * @param integer                   $priority  The command priority
     * @return KCommandChain
     */
    public function setHandlerPriority(KCommandHandlerInterface $handler, $priority)
    {
        $this->__queue->setPriority($handler, $priority);
        return $this;
    }

    /**
     * Get the priority of a command
     *
     * @param   KCommandHandlerInterface $handler A command handler
     * @return integer The command priority
     */
    public function getHandlerPriority(KCommandHandlerInterface $handler)
    {
        return $this->__queue->getPriority($handler);
    }

    /**
     * Set the break condition
     *
     * @param mixed|null $condition The break condition, or NULL to set reset the break condition
     * @return KCommandChain
     */
    public function setBreakCondition($condition)
    {
        $this->_break_condition = $condition;
        return $this;
    }

    /**
     * Get the break condition
     *
     * @return mixed|null   Returns the break condition, or NULL if not break condition is set.
     */
    public function getBreakCondition()
    {
        return $this->_break_condition;
    }

    /**
     * Check of the command chain is enabled
     *
     * @return bool
     */
    public function isEnabled()
    {
        return $this->__enabled;
    }
}

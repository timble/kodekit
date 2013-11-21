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
class KCommandChain extends KObjectQueue implements KCommandChainInterface
{
    /**
     * Enabled status of the chain
     *
     * @var boolean
     */
    protected $_enabled = true;

    /**
     * The chain stack
     *
     * Used to track recursive chain nesting.
     *
     * @var KObjectStack
     */
    private $__stack;

    /**
     * Constructor
     *
     * @param KObjectConfig  $config  An optional KObjectConfig object with configuration options
     * @return KCommandChain
     */
    public function __construct(KObjectConfig $config)
    {
        parent::__construct($config);

        $this->_enabled = (boolean) $config->enabled;
        $this->__stack = $this->getObject('koowa:object.stack');
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
            'enabled'   => true
        ));

        parent::_initialize($config);
    }

    /**
     * Attach a command to the chain
     *
     * The priority parameter can be used to override the command priority while enqueueing the command.
     *
     * @param   KCommandInvokerInterface|KObjectHandlable   $invoker
     * @param   integer            $priority The command priority, usually between 1 (high priority) and 5 (lowest),
     *                                        default is 3. If no priority is set, the command priority will be used
     *                                        instead.
     * @return KCommandChain
     * @throws \InvalidArgumentException if the object does not implement KCommandInvokerInterface
     */
    public function enqueue(KObjectHandlable $invoker, $priority = null)
    {
        if (!$invoker instanceof KCommandInvokerInterface) {
            throw new InvalidArgumentException('Invoker needs to implement KCommandInvokerInterface');
        }

        $priority = is_int($priority) ? $priority : $invoker->getPriority();
        return parent::enqueue($invoker, $priority);
    }

    /**
     * Removes a command from the queue
     *
     * @param   KObjectHandlable $invoker
     * @return  boolean    TRUE on success FALSE on failure
     * @throws  \InvalidArgumentException if the object does not implement KCommandInvokerInterface
     */
    public function dequeue(KObjectHandlable $invoker)
    {
        if (!$invoker instanceof KCommandInvokerInterface) {
            throw new InvalidArgumentException('Invoker needs to implement KCommandInvokerInterface');
        }

        return parent::dequeue($invoker);
    }

    /**
     * Check if the queue does contain a given object
     *
     * @param  KObjectHandlable $invoker
     * @return bool
     * @throws  \InvalidArgumentException if the object does not implement KCommandInvokerInterface
     */
    public function contains(KObjectHandlable $invoker)
    {
        if (!$invoker instanceof KCommandInvokerInterface) {
            throw new InvalidArgumentException('Invoker needs to implement KCommandInvokerInterface');
        }

        return parent::contains($invoker);
    }

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
    public function run($name, KCommandInterface $command, $condition = null)
    {
        if ($this->_enabled)
        {
            $this->__stack->push(clone $this);

            foreach ($this->__stack->top() as $invoker)
            {
                if($condition === self::CONDITION_EXCEPTION)
                {
                    try
                    {
                        $invoker->execute($name, $command);
                    }
                    catch (KCommandExceptionInvoker $e)
                    {
                        $this->__stack->pop();
                        return $e;
                    }
                }
                else
                {
                    $result = $invoker->execute($name, $command);

                    if($condition !== null && $result === $condition)
                    {
                        $this->__stack->pop();
                        return $condition;
                    }
                }
            }

            $this->__stack->pop();
        }

        return null;
    }

    /**
     * Enable the chain
     *
     * @return  $this
     */
    public function enable()
    {
        $this->_enabled = true;

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
        $this->_enabled = false;

        return $this;
    }

    public function setPriority(KObjectHandlable $invoker, $priority)
    {
        if (!$invoker instanceof KCommandInvokerInterface) {
            throw new InvalidArgumentException('Command needs to implement KCommandInvokerInterface');
        }

        return parent::setPriority($invoker, $priority);
    }

    public function getPriority(KObjectHandlable $invoker)
    {
        if (!$invoker instanceof KCommandInvokerInterface) {
            throw new InvalidArgumentException('Command needs to implement KCommandInvokerInterface');
        }

        return parent::getPriority($invoker);
    }
}

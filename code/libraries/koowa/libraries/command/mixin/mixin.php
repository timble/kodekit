<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright	Copyright (C) 2007 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/koowa for the canonical source repository
 */

/**
 * Command Mixin
 *
 * Class can be used as a mixin in classes that want to implement a chain of responsibility or chain of command pattern.
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Koowa\Library\Command\Mixin
 */
class KCommandMixin extends KCommandCallbackAbstract implements KCommandMixinInterface, KCommandHandlerInterface
{
    /**
     * Chain of command object
     *
     * @var KCommandChainInterface
     */
    private $__command_chain;

    /**
     * List of command handlers
     *
     * Associative array of command handlers, where key holds the handlers identifier string
     * and the value is an identifier object.
     *
     * @var array
     */
    private $__command_handlers = array();

    /**
     * The command priority
     *
     * @var integer
     */
    protected $_priority;

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
        $this->__command_chain = $config->command_chain;

        //Add the event subscribers
        $handlers = (array) KObjectConfig::unbox($config->command_handlers);

        foreach ($handlers as $key => $value)
        {
            if (is_numeric($key)) {
                $this->addCommandHandler($value);
            } else {
                $this->addCommandHandler($key, $value);
            }
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
            'command_handlers'  => array(),
            'priority'          => self::PRIORITY_NORMAL,
        ));

        parent::_initialize($config);
    }

    /**
     * Mixin Notifier
     *
     * This function is called when the mixin is being mixed. It will get the mixer passed in.
     *
     * @param KObjectMixable $mixer The mixer object
     * @return void
     */
    public function onMixin(KObjectMixable $mixer)
    {
        parent::onMixin($mixer);

        //Add mixer to the command chain to be able to execute the registered command handlers.
        $this->addCommandHandler($this);
    }

    /**
     * Execute the callbacks
     *
     * @param KCommandInterface         $command    The command
     * @param KCommandChainInterface    $chain      The chain executing the command
     * @return mixed|null If a handler breaks, returns the break condition. NULL otherwise.
     */
    public function execute(KCommandInterface $command, KCommandChainInterface $chain)
    {
        return parent::invokeCallbacks($command, $this->getMixer());
    }

    /**
     * Invoke a command by calling all registered handlers
     *
     * If a command handler returns the 'break condition' the executing is halted. If no break condition is specified the
     * the command chain will execute all command handlers, regardless of the handler result returned.
     *
     * @param  string|KCommandInterface  $command    The command name or a KCommandInterface object
     * @param  array|Traversable         $attributes An associative array or a Traversable object
     * @param  KObjectInterface          $subject    The command subject
     * @return mixed|null If a handler breaks, returns the break condition. NULL otherwise.
     */
    public function invokeCommand($command, $attributes = null, $subject = null)
    {
        return $this->getCommandChain()->execute($command, $attributes, $subject);
    }

    /**
     * Get the chain of command object
     *
     * @throws UnexpectedValueException
     * @return  KCommandChainInterface
     */
    public function getCommandChain()
    {
        if(!$this->__command_chain instanceof KCommandChainInterface)
        {
            $config = array('break_condition' => $this->getBreakCondition());
            $this->__command_chain = $this->getObject($this->__command_chain, $config);

            if(!$this->__command_chain instanceof KCommandChainInterface)
            {
                throw new UnexpectedValueException(
                    'CommandChain: '.get_class($this->__command_chain).' does not implement KCommandChainInterface'
                );
            }
        }

        return $this->__command_chain;
    }

    /**
     * Set the chain of command object
     *
     * @param   KCommandChainInterface $chain A command chain object
     * @return  KObjectInterface The mixer object
     */
    public function setCommandChain(KCommandChainInterface $chain)
    {
        $this->__command_chain = $chain;
        return $this->getMixer();
    }

    /**
     * Add a command callback
     *
     * If the handler has already been added. It will not be re-added but parameters will be merged. This allows to
     * change or add parameters for existing handlers.
     *
     * @param  	string          $command  The command name to register the handler for
     * @param 	string|Closure  $method   The name of the method or a Closure object
     * @param   array|object    $params   An associative array of config parameters or a KObjectConfig object
     * @throws  InvalidArgumentException If the method does not exist
     * @return  KCommandMixin
     */
    public function addCommandCallback($command, $method, $params = array())
    {
        if (is_string($method) && !method_exists($this->getMixer(), $method))
        {
            throw new InvalidArgumentException(
                'Method does not exist '.get_class().'::'.$method
            );
        }

        return parent::addCommandCallback($command, $method, $params);
    }

    /**
     * Attach a command to the chain
     *
     * The priority parameter can be used to override the command priority while enqueueing the command.
     *
     * @param  mixed $handler An object that implements KCommandHandlerInterface, an KObjectIdentifier
     *                        or valid identifier string
     * @param  array  $config  An optional associative array of configuration options
     * @return  KObjectInterface The mixer object
     */
    public function addCommandHandler($handler, $config = array())
    {
        if (!($handler instanceof KCommandHandlerInterface)) {
            $handler = $this->getCommandHandler($handler, $config);
        }

        $this->getCommandChain()->addHandler($handler);
        return $this->getMixer();
    }

    /**
     * Removes a command from the chain
     *
     * @param   KCommandHandlerInterface  $handler  The command handler
     * @return   KObjectInterface The mixer object
     */
    public function removeCommandHandler(KCommandHandlerInterface $handler)
    {
        $this->getCommandChain()->removeHandler($handler);
        return $this->getMixer();
    }

    /**
     * Get a command handler by identifier
     *
     * @param  mixed $handler An object that implements ObjectInterface, ObjectIdentifier object
     *                        or valid identifier string
     * @param  array  $config An optional associative array of configuration settings
     * @throws UnexpectedValueException    If the handler is not implementing the KCommandHandlerInterface
     * @return KCommandHandlerInterface
     */
    public function getCommandHandler($handler, $config = array())
    {
        if (!($handler instanceof KObjectIdentifier))
        {
            //Create the complete identifier if a partial identifier was passed
            if (is_string($handler) && strpos($handler, '.') === false)
            {
                $identifier = $this->getIdentifier()->toArray();
                $identifier['path'] = array('command', 'handler');
                $identifier['name'] = $handler;

                $identifier = $this->getIdentifier($identifier);
            }
            else $identifier = $this->getIdentifier($handler);
        }
        else $identifier = $handler;

        if (!isset($this->__command_handlers[(string)$identifier]))
        {
            $handler = $this->getObject($identifier, $config);

            if (!($handler instanceof KCommandHandlerInterface))
            {
                throw new UnexpectedValueException(
                    "Command Handler $identifier does not implement KCommandHandlerInterface"
                );
            }
        }
        else $handler = $this->__command_handlers[(string)$identifier];

        return $handler;
    }

    /**
     * Gets the command handlers
     *
     * @return array An array of command handlers
     */
    public function getCommandHandlers()
    {
        return $this->getCommandChain()->getHandlers()->toArray();
    }

    /**
     * Get the methods that are available for mixin
     *
     * @param  KObjectMixable $mixer     The mixer requesting the mixable methods.
     * @param  array          $exclude   A list of methods to exclude
     * @return array An array of methods
     */
    public function getMixableMethods(KObjectMixable $mixer = null, $exclude = array())
    {
        $exclude += array('execute', 'getPriority', 'setBreakCondition', 'getBreakCondition', 'invokeCommandCallbacks');
        return parent::getMixableMethods($mixer, $exclude);
    }

    /**
     * Get the priority of the handler
     *
     * @return	integer The handler priority
     */
    public function getPriority()
    {
        return $this->_priority;
    }
}
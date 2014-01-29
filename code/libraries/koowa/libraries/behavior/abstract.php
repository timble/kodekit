<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright	Copyright (C) 2007 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/koowa for the canonical source repository
 */

/**
 * Abstract Behavior
 *
 * The abstract behavior will translate the command name to a method name format (eg, _before[Command] or _after[Command])
 * and add execute the method. Command handlers should be declared protected.
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Koowa\Library\Command
 */
abstract class KBehaviorAbstract extends KCommandCallbackAbstract implements KBehaviorInterface
{
    /**
     * The object identifier
     *
     * @var KObjectIdentifier
     */
    private $__object_identifier;

    /**
     * The object manager
     *
     * @var KObjectManager
     */
    private $__object_manager;

    /**
     * The object config
     *
     * @var KObjectConfig
     */
    private $__object_config;

    /**
     * The behavior priority
     *
     * @var integer
     */
    protected $_priority;

    /**
     * Constructor.
     *
     * @param  KObjectConfig $config A ObjectConfig object with configuration options
     * @throws InvalidArgumentException
     */
    public function __construct(KObjectConfig $config)
    {
        //Set the object manager
        if (!$config->object_manager instanceof KObjectManagerInterface)
        {
            throw new InvalidArgumentException(
                'object_manager [ObjectManagerInterface] config option is required, "'.gettype($config->object_manager).'" given.'
            );
        }
        else $this->__object_manager = $config->object_manager;

        //Set the object identifier
        if (!$config->object_identifier instanceof KObjectIdentifierInterface)
        {
            throw new InvalidArgumentException(
                'object_identifier [ObjectIdentifierInterface] config option is required, "'.gettype($config->object_identifier).'" given.'
            );
        }
        else $this->__object_identifier = $config->object_identifier;

        parent::__construct($config);

        //Set the object config
        $this->__object_config = $config;

        //Set the command priority
        $this->_priority = $config->priority;
    }

    /**
     * Initializes the options for the object
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param  KObjectConfig $config A ObjectConfig object with configuration options
     * @return void
     */
    protected function _initialize(KObjectConfig $config)
    {
        $config->append(array(
            'priority'   => self::PRIORITY_NORMAL,
        ));

        parent::_initialize($config);
    }

    /**
     * Command handler
     *
     * @param KCommandInterface         $command    The command
     * @param KCommandChainInterface    $chain      The chain executing the command
     * @return mixed If a handler breaks, returns the break condition. Returns the result of the handler otherwise.
     */
    public function execute(KCommandInterface $command, KCommandChainInterface $chain)
    {
        $parts  = explode('.', $command->getName());
        $method = '_'.$parts[0].ucfirst($parts[1]);

        if(method_exists($this, $method)) {
            $result = $this->$method($command);
        } else {
            $result = parent::invokeCallbacks($command, $this);
        }

        return $result;
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
     * @return  KCommandHandlerAbstract
     */
    public function addCommandCallback($command, $method, $params = array())
    {
        if (is_string($method) && !method_exists($this, $method))
        {
            throw new InvalidArgumentException(
                'Method does not exist '.__CLASS__.'::'.$method
            );
        }

        return parent::addCommandCallback($command, $method, $params);
    }

    /**
     * Get the priority of a behavior
     *
     * @return  integer The command priority
     */
    public function getPriority()
    {
        return $this->_priority;
    }

    /**
     * Get the behavior name
     *
     * @return string
     */
    public function getName()
    {
        return $this->getIdentifier()->name;
    }

    /**
     * Get an object handle
     *
     * @return string A string that is unique, or NULL
     * @see execute()
     */
    public function getHandle()
    {
        foreach($this->getMethods() as $method)
        {
            if (substr($method, 0, 7) == '_before' || substr($method, 0, 6) == '_after') {
                return KObjectMixinAbstract::getHandle();
            }
        }

        return parent::getHandle();
    }

    /**
     * Get the methods that are available for mixin based
     *
     * This function also dynamically adds a function of format is[Behavior] to allow client code to check if the
     * behavior is callable.
     *
     * @param  KObjectMixable $mixer The mixer requesting the mixable methods.
     * @return array An array of methods
     */
    public function getMixableMethods(KObjectMixable $mixer = null)
    {
        $methods   = parent::getMixableMethods($mixer);
        $methods['is'.ucfirst($this->getIdentifier()->name)] = 'is'.ucfirst($this->getIdentifier()->name);

        $excluded = array('execute', 'invokeCallbacks', 'getIdentifier', 'getPriority', 'getHandle', 'getName',
            'getObject', 'setBreakCondition', 'getBreakCondition', 'addCommandCallback', 'removeCommandCallback');

        return array_diff_key($methods, array_fill_keys($excluded, $excluded));
    }

    /**
     * Get an instance of an object identifier
     *
     * @param KObjectIdentifier|string $identifier An ObjectIdentifier or valid identifier string
     * @param array  			      $config     An optional associative array of configuration settings.
     * @return KObjectInterface  Return object on success, throws exception on failure.
     */
    final public function getObject($identifier, array $config = array())
    {
        $result = $this->__object_manager->getObject($identifier, $config);
        return $result;
    }

    /**
     * Gets the service identifier.
     *
     * If no identifier is passed the object identifier of this object will be returned. Function recursively
     * resolves identifier aliases and returns the aliased identifier.
     *
     * @param   string|object    $identifier The class identifier or identifier object
     * @return  KObjectIdentifier
     */
    final public function getIdentifier($identifier = null)
    {
        if (isset($identifier)) {
            $result = $this->__object_manager->getIdentifier($identifier);
        } else {
            $result = $this->__object_identifier;
        }

        return $result;
    }
}
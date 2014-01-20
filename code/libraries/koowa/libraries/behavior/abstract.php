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
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Koowa\Library\Command
 */
abstract class KBehaviorAbstract extends KObjectMixinAbstract implements KBehaviorInterface
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
     * Array of command handlers
     *
     * $var array
     */
    private $__command_handlers = array();

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

        //Automatically mixin the behavior
        if ($config->auto_mixin) {
            $this->mixin($this);
        }
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
            'auto_mixin' => false
        ));

        parent::_initialize($config);
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
     * Command handler
     *
     * @param KCommandInterface $command    The command
     * @param  mixed            $condition  The break condition
     * @return array|mixed Returns an array of the callback results in FIFO order. If a handler breaks and the break
     *                     condition is not NULL returns the break condition.
     */
    public function executeCommand(KCommandInterface $command, $condition = null)
    {
        $result = array();

        if(isset($this->__command_handlers[$command->getName()]))
        {
            foreach($this->__command_handlers[$command->getName()] as $handler)
            {
                $method = $handler['method'];
                $params = $handler['params'];

                try
                {
                    if(class_exists('Closure') && $method instanceof Closure) {
                        $result[] = $method($command->append($params));
                    } else {
                        $result[$method] = $this->$method($command->append($params));
                    }
                }
                catch (KBehaviorExceptionHandler $e) {
                    $result[] = $e;
                }

                if($condition !== null && current($result) === $condition)
                {
                    $result = current($result);
                    break;
                }
            }
        }

        return $result;
    }

    /**
     * Add a command handler
     *
     * If the handler has already been added. It will not be re-added but parameters will be merged. This allows to
     * change or add parameters for existing handlers.
     *
     * @param  	string          $command  The command name to register the handler for
     * @param 	string|Closure  $method   The name of the method or a Closure object
     * @param   array|object    An associative array of config parameters or a KObjectConfig object
     * @throws  InvalidArgumentException If the callback is not a callable
     * @return  KCommandInvokerAbstract
     */
    public function addCommandHandler($command, $method, $params = array())
    {
        if (is_string($method) && !method_exists($this, $method))
        {
            throw new InvalidArgumentException(
                'Method does not exist '.__CLASS__.'::'.$method
            );
        }

        $params  = (array) KObjectConfig::unbox($params);
        $command = strtolower($command);

        if (!isset($this->__command_handlers[$command]) ) {
            $this->__command_handlers[$command] = array();
        }

        if(class_exists('Closure') && $method instanceof Closure) {
            $index = spl_object_hash($method);
        } else {
            $index = $method;
        }

        if(!isset($this->__command_handlers[$command][$index]))
        {
            $this->__command_handlers[$command][$index]['method'] = $method;
            $this->__command_handlers[$command][$index]['params'] = $params;
        }
        else  $this->__command_handlers[$command][$index]['params'] = array_merge($this->__command_handlers[$command][$index]['params'], $params);

        return $this;
    }

    /**
     * Remove a command handler
     *
     * @param  	string	        $command  The command to unregister the handler from
     * @param 	string|Closure	$method   The name of the method or a Closure object to unregister
     * @return  KCommandInvokerAbstract
     */
    public function removeCommandHandler($command, $method)
    {
        $command = strtolower($command);

        if (isset($this->__command_handlers[$command]) )
        {
            if(class_exists('Closure') && $method instanceof Closure) {
                $index = spl_object_hash($method);
            } else {
                $index = $method;
            }

            unset($this->__command_handlers[$command][$index]);
        }

        return $this;
    }

    /**
     * Get an object handle
     *
     * Function will return a valid object handle if one or more command handlers have been registered. If no command
     * handlers are registered the function will return NULL.
     *
     * @return string A string that is unique, or NULL
     * @see executeCommand()
     */
    public function getHandle()
    {
        if(!empty($this->__command_handlers)) {
            return KObjectMixinAbstract::getHandle();
        }

        return null;
    }

    /**
     * Get the handlers for a command
     *
     * @param string $command   The command
     * @return  array An array of command handlers
     */
    public function getCommandHandlers($command)
    {
        $result = array();
        if (isset($this->__command_handlers[$command]) ) {
            $result = array_values($this->__command_handlers[$command]);
        }

        return $result;
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

        return array_diff($methods, array('executeCommand', 'getIdentifier', 'getPriority', 'getHandle', 'getName', 'getObject', 'getIdentifier', 'addCommandHandler', 'removeCommandHandler', 'getCommandHandlers'));
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
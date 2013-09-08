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
     * The behavior priority
     *
     * @var integer
     */
    protected $_priority;

    /**
     * The service identifier
     *
     * @var KObjectIdentifier
     */
    private $__object_identifier;

    /**
     * The service manager
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
     * Constructor.
     *
     * @param  KObjectConfig $config  A ObjectConfig object with configuration options
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
            'priority'   => KCommand::PRIORITY_NORMAL,
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
     * This function translated the command name to a command handler function of the format '_before[Command]' or
     * '_after[Command]. Command handler functions should be declared protected.
     *
     * @param   string           $name     The command name
     * @param   KCommandContext  $context  The command context
     *
     * @return  mixed  Method result if the method exists, NULL otherwise.
     */
    public function execute($name, KCommandContext $context)
    {
        $result = null;

        $identifier = clone $context->getSubject()->getIdentifier();
        $type = array_pop($identifier->path);

        $parts = explode('.', $name);
        $method = '_' . $parts[0] . ucfirst($type) . ucfirst($parts[1]);

        //If the method exists call the method and return the result
        if (method_exists($this, $method)) {
            $result = $this->$method($context);
        }

        return $result;
    }

    /**
     * Get an object handle
     *
     * This function only returns a valid handle if one or more command handler functions are defined. A commend handler
     * function needs to follow the following format : '_afterX[Event]' or '_beforeX[Event]' to be recognised.
     *
     * @return string A string that is unique, or NULL
     * @see execute()
     */
    public function getHandle()
    {
        $methods = $this->getMethods();

        foreach ($methods as $method)
        {
            if (substr($method, 0, 7) == '_before' || substr($method, 0, 6) == '_after') {
                return parent::getHandle();
            }
        }

        return null;
    }

    /**
     * Get the methods that are available for mixin based
     *
     * This function also dynamically adds a function of format is[Behavior] to allow client code to check if the
     * behavior is callable.
     *
     * @param  KObjectInterface $mixer The mixer requesting the mixable methods.
     * @return array An array of methods
     */
    public function getMixableMethods(KObjectMixable $mixer = null)
    {
        $methods   = parent::getMixableMethods($mixer);
        $methods['is'.ucfirst($this->getIdentifier()->name)] = 'is'.ucfirst($this->getIdentifier()->name);

        return array_diff($methods, array('execute', 'getIdentifier', 'getPriority', 'getHandle', 'getObject', 'getIdentifier'));
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
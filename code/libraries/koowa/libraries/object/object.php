<?php
/**
 * Nooku Framework - http://nooku.org/framework
 *
 * @copyright   Copyright (C) 2007 - 2014 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/nooku/nooku-framework for the canonical source repository
 */

/**
 * Object
 *
 * Provides getters and setters, mixin, object handles
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Koowa\Library\Object
 */
class KObject implements KObjectInterface, KObjectMixable, KObjectHandlable, KObjectDecoratable
{
    /**
     * Class methods
     *
     * @var array
     */
    private $__methods = array();

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
     * Mixed in methods
     *
     * @var array
     */
    protected $_mixed_methods = array();

    /**
     * Constructor.
     *
     * @param   KObjectConfig $config Configuration options
     */
    public function __construct( KObjectConfig $config)
    {
        //Set the object manager
        if(isset($config->object_manager)) {
            $this->__object_manager = $config->object_manager;
        }

        //Set the object identifier
        if(isset($config->object_identifier)) {
            $this->__object_identifier = $config->object_identifier;
        }

        //Initialise the object
        $this->_initialize($config);

        //Add the mixins
        $mixins = (array) KObjectConfig::unbox($config->mixins);

        foreach ($mixins as $key => $value)
        {
            if (is_numeric($key)) {
                $this->mixin($value);
            } else {
                $this->mixin($key, $value);
            }
        }

        //Set the object config
        $this->__object_config = $config;
    }

    /**
     * Initializes the options for the object
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param   KObjectConfig $config Configuration options.
     * @return  void
     */
    protected function _initialize(KObjectConfig $config)
    {
        $config->append(array(
            'mixins'     => array(),
            'decorators' => array(),
        ));
    }

    /**
     * Mixin an object
     *
     * When using mixin(), the calling object inherits the methods of the mixed in objects, in a LIFO order.
     *
     * @param   mixed $mixin   An KObjectIdentifier, identifier string or object implementing KObjectMixinInterface
     * @param   array $config  An optional associative array of configuration options
     * @return  KObjectMixinInterface
     * @throws  KObjectExceptionInvalidIdentifier If the identifier is not valid
     * @throws  UnexpectedValueException If the mixin does not implement the KObjectMixinInterface
     */
    public function mixin($mixin, $config = array())
    {
        if (!($mixin instanceof KObjectMixinInterface))
        {
            if (!($mixin instanceof KObjectIdentifier)) {
               $identifier = $this->getIdentifier($mixin);
            } else {
                $identifier = $mixin;
            }

            $config = new KObjectConfig($config);
            $config->mixer = $this;

            $class = $this->getObject('manager')->getClass($identifier);
            $mixin = new $class($config);
        }

        /*
        * Check if the mixin extends from ObjectMixin to ensure it's implementing the
        * ObjectMixinInterface and ObjectHandable interfaces.
        */
        if(!$mixin instanceof KObjectMixinInterface)
        {
            throw new UnexpectedValueException(
                'Mixin: '.get_class($mixin).' does not implement KObjectMixinInterface'
            );
        }

        //Notify the mixin
        $mixin->onMixin($this);

        //Set the mixed methods
        $mixed_methods = $mixin->getMixableMethods();

        if(!empty($mixed_methods))
        {
            foreach($mixed_methods as $name => $method) {
                $this->_mixed_methods[$name] = $method;
            }

            //Set the object methods, native methods have precedence over mixed methods
            $mixed_methods = array_keys($mixed_methods);
            $mixed_methods = array_combine($mixed_methods, $mixed_methods);

            $this->__methods = array_merge($mixed_methods, $this->getMethods());
        }

        return $mixin;
    }

    /**
     * Decorate the object
     *
     * When using decorate(), the object will be decorated by the decorator. The decorator needs to extend from
     * KObjectDecorator.
     *
     * @param   mixed $decorator An KObjectIdentifier, identifier string or object implementing KObjectDecorator
     * @param   array $config    An optional associative array of configuration options
     * @return  KObjectDecoratorInterface
     * @throws  KObjectExceptionInvalidIdentifier If the identifier is not valid
     * @throws  UnexpectedValueException If the decorator does not extend from KObjectDecorator
     */
    public function decorate($decorator, $config = array())
    {
        if (!($decorator instanceof KObjectDecorator))
        {
            if (!($decorator instanceof KObjectIdentifier)) {
                $identifier = $this->getIdentifier($decorator);
            } else {
                $identifier = $decorator;
            }

            $config = new KObjectConfig($config);
            $config->delegate = $this;

            $class     = $this->getObject('manager')->getClass($identifier);
            $decorator = new $class($config);
        }

        /*
         * Check if the decorator extends from KObjectDecorator to ensure it's implementing the
         * KObjectInterface, KObjectHandable, ObjectMixable and KObjectDecoratable interfaces.
         */
        if(!$decorator instanceof KObjectDecorator)
        {
            throw new UnexpectedValueException(
                'Decorator: '.get_class($decorator).' does not extend from KObjectDecorator'
            );
        }

        //Notify the decorator
        $decorator->onDecorate($this);

        return $decorator;
    }

    /**
     * Checks if the object or one of its mixins inherits from a class.
     *
     * @param   string|object   The class to check
     * @return  boolean         Returns TRUE if the object inherits from the class
     */
    public function inherits($class)
    {
        if ($this instanceof $class) {
            return true;
        }

        $objects = array_values($this->_mixed_methods);

        foreach($objects as $object)
        {
            if($object instanceof $class) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get a handle for this object
     *
     * This function returns an unique identifier for the object. This id can be used as a hash key for storing objects
     * or for identifying an object
     *
     * @return string A string that is unique
     */
    public function getHandle()
    {
        return spl_object_hash( $this );
    }

    /**
     * Get a list of all the available methods
     *
     * This function returns an array of all the methods, both native and mixed in
     *
     * @return array An array
     */
    public function getMethods()
    {
        if (!$this->__methods)
        {
            $methods = array();

            $reflection = new ReflectionClass($this);
            foreach ($reflection->getMethods() as $method) {
                $methods[$method->name] = $method->name;
            }

            $this->__methods = $methods;
        }

        return $this->__methods;
    }

    /**
     * Get an instance of a class based on a class identifier only creating it if it doesn't exist yet.
     *
     * @param   mixed $identifier An KObjectIdentifier, identifier string or object implementing KObjectInterface
     * @param	array $config     An optional associative array of configuration settings.
     * @return	KObjectInterface Return object on success, throws exception on failure
     * @throws	RuntimeException if the object manager has not been defined.
     * @see 	KObjectInterface
     */
    final public function getObject($identifier, array $config = array())
    {
        if(!isset($this->__object_manager)) {
            throw new RuntimeException("Failed to call ".get_class($this)."::getObject(). No object_manager object defined.");
        }

        return $this->__object_manager->getObject($identifier, $config);
    }

    /**
     * Gets the object identifier.
     *
     * @param  mixed $identifier An KObjectIdentifier, identifier string or object implementing KObjectInterface
     * @return KObjectIdentifier
     * @throws	RuntimeException if the object manager has not been defined.
     * @see 	KObjectInterface
     */
    final public function getIdentifier($identifier = null)
    {
        if(isset($identifier))
        {
            if(!isset($this->__object_manager)) {
                throw new RuntimeException("Failed to call ".get_class($this)."::getIdentifier(). No object_manager object defined.");
            }

            $result = $this->__object_manager->getIdentifier($identifier);
        }
        else  $result = $this->__object_identifier;

        return $result;
    }

    /**
     * Get the object configuration
     *
     * If no identifier is passed the object config of this object will be returned. Function recursively
     * resolves identifier aliases and returns the aliased identifier.
     *
     * @param  mixed $identifier An ObjectIdentifier, identifier string or object implementing ObjectInterface
     * @return KObjectConfig
     */
    final public function getConfig($identifier = null)
    {
        if (isset($identifier)) {
            $result = $this->__object_manager->getIdentifier($identifier)->getConfig();
        } else {
            $result = $this->__object_config;
        }

        return $result;
    }

    /**
     * Preform a deep clone of the object.
     *
     * @return void
     */
    public function __clone()
    {
        foreach ($this->_mixed_methods as $method => $object)
        {
            if (is_object($object) && !($object instanceof Closure)){
                $this->_mixed_methods[$method] = clone $object;
            }
        }
    }

    /**
     * Search the mixin method map and call the method or trigger an error
     *
     * @param  string   $method    The function name
     * @param  array    $arguments The function arguments
     * @return mixed The result of the function
     * @throws BadMethodCallException   If method could not be found
     */
    public function __call($method, $arguments)
    {
        if (isset($this->_mixed_methods[$method]))
        {
            $result = null;

            if ($this->_mixed_methods[$method] instanceof Closure)
            {
                $closure = $this->_mixed_methods[$method];

                switch (count($arguments)) {
                    case 0 :
                        $result = $closure();
                        break;
                    case 1 :
                        $result = $closure($arguments[0]);
                        break;
                    case 2 :
                        $result = $closure($arguments[0], $arguments[1]);
                        break;
                    case 3 :
                        $result = $closure($arguments[0], $arguments[1], $arguments[2]);
                        break;
                    default:
                        // Resort to using call_user_func_array for many segments
                        $result = call_user_func_array($closure, $arguments);
                }
            }
            elseif(is_object($this->_mixed_methods[$method]))
            {
                $mixin = $this->_mixed_methods[$method];

                //Switch the mixin's attached mixer
                $mixin->setMixer($this);

                // Call_user_func_array is ~3 times slower than direct method calls.
                switch (count($arguments))
                {
                    case 0 :
                        $result = $mixin->$method();
                        break;
                    case 1 :
                        $result = $mixin->$method($arguments[0]);
                        break;
                    case 2 :
                        $result = $mixin->$method($arguments[0], $arguments[1]);
                        break;
                    case 3 :
                        $result = $mixin->$method($arguments[0], $arguments[1], $arguments[2]);
                        break;
                    default:
                        // Resort to using call_user_func_array for many segments
                        $result = call_user_func_array(array($mixin, $method), $arguments);
                }
            }
            else $result = $this->_mixed_methods[$method];

            return $result;
        }

        throw new BadMethodCallException('Call to undefined method :' . $method);
    }
}

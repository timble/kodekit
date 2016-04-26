<?php
/**
 * Kodekit - http://timble.net/kodekit
 *
 * @copyright   Copyright (C) 2007 - 2016 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license     MPL v2.0 <https://www.mozilla.org/en-US/MPL/2.0>
 * @link        https://github.com/timble/kodekit for the canonical source repository
 */

namespace Kodekit\Library;

/**
 * Object
 *
 * Provides getters and setters, mixin, object handles
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Kodekit\Library\Object
 */
class Object implements ObjectInterface, ObjectMixable, ObjectHandlable, ObjectDecoratable
{
    /**
     * Class methods
     *
     * @var array
     */
    private $__methods = array();

    /**
     * Mixed in methods
     *
     * @var array
     */
    private $__mixed_methods = array();

    /**
     * The object identifier
     *
     * @var ObjectIdentifier
     */
    private $__object_identifier;

    /**
     * The object manager
     *
     * @var ObjectManager
     */
    private $__object_manager;

    /**
     * The object config
     *
     * @var ObjectConfig
     */
    private $__object_config;

    /**
     * Constructor.
     *
     * @param   ObjectConfig $config Configuration options
     */
    public function __construct( ObjectConfig $config)
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
        $mixins = (array) ObjectConfig::unbox($config->mixins);

        foreach ($mixins as $key => $value)
        {
            if (is_numeric($key)) {
                $this->mixin($value);
            } else {
                $this->mixin($key, $value);
            }
        }

        //Register the decorators
        $decorators = (array) ObjectConfig::unbox($config->decorators);

        foreach ($decorators as $key => $value)
        {
            if (is_numeric($key)) {
                $this->getIdentifier()->getDecorators()->append(array($value));
            } else {
                $this->getIdentifier()->getDecorators()->append(array($key, $value));
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
     * @param   ObjectConfig $config Configuration options.
     * @return  void
     */
    protected function _initialize(ObjectConfig $config)
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
     * @param   mixed $mixin   An ObjectIdentifier, identifier string or object implementing ObjectMixinInterface
     * @param   array $config  An optional associative array of configuration options
     * @return  ObjectMixinInterface
     * @throws  ObjectExceptionInvalidIdentifier If the identifier is not valid
     * @throws  \UnexpectedValueException If the mixin does not implement the ObjectMixinInterface
     */
    public function mixin($mixin, $config = array())
    {
        if (!($mixin instanceof ObjectMixinInterface))
        {
            if (!($mixin instanceof ObjectIdentifier)) {
               $identifier = $this->getIdentifier($mixin);
            } else {
                $identifier = $mixin;
            }

            $config = new ObjectConfig($config);
            $config->mixer = $this;

            $class = $this->getObject('manager')->getClass($identifier);
            $mixin = new $class($config);
        }

        /*
        * Check if the mixin extends from ObjectMixin to ensure it's implementing the
        * ObjectMixinInterface and ObjectHandable interfaces.
        */
        if(!$mixin instanceof ObjectMixinInterface)
        {
            throw new \UnexpectedValueException(
                'Mixin: '.get_class($mixin).' does not implement ObjectMixinInterface'
            );
        }

        //Notify the mixin
        $mixin->onMixin($this);

        //Set the mixed methods
        $mixed_methods = $mixin->getMixableMethods();

        if(!empty($mixed_methods))
        {
            foreach($mixed_methods as $name => $method) {
                $this->__mixed_methods[$name] = $method;
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
     * ObjectDecorator.
     *
     * @param   mixed $decorator An ObjectIdentifier, identifier string or object implementing ObjectDecorator
     * @param   array $config    An optional associative array of configuration options
     * @return  ObjectDecoratorInterface
     * @throws  ObjectExceptionInvalidIdentifier If the identifier is not valid
     * @throws  \UnexpectedValueException If the decorator does not extend from ObjectDecorator
     */
    public function decorate($decorator, $config = array())
    {
        if (!($decorator instanceof ObjectDecorator))
        {
            if (!($decorator instanceof ObjectIdentifier)) {
                $identifier = $this->getIdentifier($decorator);
            } else {
                $identifier = $decorator;
            }

            $config = new ObjectConfig($config);
            $config->delegate = $this;

            $class     = $this->getObject('manager')->getClass($identifier);
            $decorator = new $class($config);
        }

        /*
         * Check if the decorator extends from ObjectDecorator to ensure it's implementing the
         * ObjectInterface, ObjectHandable, ObjectMixable and ObjectDecoratable interfaces.
         */
        if(!$decorator instanceof ObjectDecorator)
        {
            throw new \UnexpectedValueException(
                'Decorator: '.get_class($decorator).' does not extend from ObjectDecorator'
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

        $objects = array_values($this->__mixed_methods);

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

            $reflection = new \ReflectionClass($this);
            foreach ($reflection->getMethods() as $method) {
                $methods[$method->name] = $method->name;
            }

            $this->__methods = $methods;
        }

        return $this->__methods;
    }

    /**
     * Check if a mixed method exists
     *
     * @param string $name The name of the method
     * @return mixed
     */
    public function isMixedMethod($name)
    {
        return isset($this->__mixed_methods[$name]);
    }

    /**
     * Get an instance of a class based on a class identifier only creating it if it doesn't exist yet.
     *
     * @param   mixed $identifier An ObjectIdentifier, identifier string or object implementing ObjectInterface
     * @param	array $config     An optional associative array of configuration settings.
     * @return	ObjectInterface Return object on success, throws exception on failure
     * @throws	\RuntimeException if the object manager has not been defined.
     * @see 	ObjectInterface
     */
    final public function getObject($identifier, array $config = array())
    {
        if(!isset($this->__object_manager)) {
            throw new \RuntimeException("Failed to call ".get_class($this)."::getObject(). No object_manager object defined.");
        }

        return $this->__object_manager->getObject($identifier, $config);
    }

    /**
     * Gets the object identifier.
     *
     * @param  mixed $identifier An ObjectIdentifier, identifier string or object implementing ObjectInterface
     * @return ObjectIdentifier
     * @throws	\RuntimeException if the object manager has not been defined.
     * @see 	ObjectInterface
     */
    final public function getIdentifier($identifier = null)
    {
        if(isset($identifier))
        {
            if(!isset($this->__object_manager)) {
                throw new \RuntimeException("Failed to call ".get_class($this)."::getIdentifier(). No object_manager object defined.");
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
     * @return ObjectConfig
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
        foreach ($this->__mixed_methods as $method => $object)
        {
            if (is_object($object) && !($object instanceof \Closure)){
                $this->__mixed_methods[$method] = clone $object;
            }
        }
    }

    /**
     * Search the mixin method map and call the method or trigger an error
     *
     * @param  string   $method    The function name
     * @param  array    $arguments The function arguments
     * @return mixed The result of the function
     * @throws \BadMethodCallException   If method could not be found
     */
    public function __call($method, $arguments)
    {
        if (isset($this->__mixed_methods[$method]))
        {
            $result = null;

            if ($this->__mixed_methods[$method] instanceof \Closure)
            {
                $closure = $this->__mixed_methods[$method];

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
            elseif(is_object($this->__mixed_methods[$method]))
            {
                $mixin = $this->__mixed_methods[$method];

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
            else $result = $this->__mixed_methods[$method];

            return $result;
        }

        throw new \BadMethodCallException('Call to undefined method :' . $method);
    }
}

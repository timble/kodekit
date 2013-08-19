<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright	Copyright (C) 2007 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/koowa for the canonical source repository
 */

/**
 * Object
 *
 * Provides getters and setters, mixin, object handles
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Koowa\Library\Object
 */
class KObject implements KObjectInterface, KObjectMixable, KObjectHandlable
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
    protected $_mixed_methods = array();

    /**
     * The service identifier
     *
     * @var KObjectIdentifier
     */
    private $__service_identifier;

    /**
     * The object manager
     *
     * @var KObjectManager
     */
    private $__object_manager;

    /**
     * Constructor.
     *
     * @param   KObjectConfig $config Configuration options
     */
    public function __construct( KObjectConfig $config = null)
    {
        //Set the service container
        if(isset($config->object_manager)) {
            $this->__object_manager = $config->object_manager;
        }

        //Set the service identifier
        if(isset($config->service_identifier)) {
            $this->__service_identifier = $config->service_identifier;
        }

        //Initialise the object
        if($config) {
            $this->_initialize($config);
        }
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
        //do nothing
    }

    /**
     * Set the object properties
     *
     * @param   string|array|object $property The name of the property, an associative array or an object
     * @param   mixed               $value    The value of the property
     * @throws  InvalidArgumentException
     * @return  KObject
     */
    public function set( $property, $value = null )
    {
        if(is_object($property)) {
            $property = get_object_vars($property);
        }

        if(is_array($property))
        {
            foreach ($property as $k => $v) {
                $this->set($k, $v);
            }
        }
        else
        {
            if('_' == substr($property, 0, 1)) {
                throw new InvalidArgumentException("Protected or private properties can't be set outside of object scope in ".get_class($this));
            }

            $this->$property = $value;
        }

        return $this;
    }

    /**
     * Get the object properties
     *
     * If no property name is given then the function will return an associative array of all properties.
     *
     * If the property does not exist and a  default value is specified this is returned, otherwise the function
     * return NULL.
     *
     * @param   string  $property The name of the property
     * @param   mixed   $default  The default value
     * @return  mixed   The value of the property, an associative array or NULL
     */
    public function get($property = null, $default = null)
    {
        $result = $default;

        if(is_null($property))
        {
            $result  = get_object_vars($this);

            foreach ($result as $key => $value)
            {
                if ('_' == substr($key, 0, 1)) {
                    unset($result[$key]);
                }
            }
        }
        else
        {
            if(isset($this->$property)) {
                $result = $this->$property;
            }
        }

        return $result;
    }

    /**
     * Mixin an object
     *
     * When using mixin(), the calling object inherits the methods of the mixed in objects, in a LIFO order.
     *
     * @param   KObjectMixinInterface $object  An object that implements KMinxInterface
     * @param   array           $config  An optional associative array of configuration options
     * @return  KObject
     */
    public function mixin(KObjectMixinInterface $object, $config = array())
    {
        $methods = $object->getMixableMethods($this);

        foreach($methods as $method) {
            $this->_mixed_methods[$method] = $object;
        }

        //Set the mixer
        $object->setMixer($this);

        return $this;
    }

    /**
     * Checks if the object or one of it's mixins inherits from a class.
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
        if(!$this->__methods)
        {
            $methods = array();

            $reflection = new ReflectionClass($this);
            foreach($reflection->getMethods() as $method) {
                $methods[] = $method->name;
            }

            $this->__methods = array_merge($methods, array_keys($this->_mixed_methods));
        }

        return $this->__methods;
    }

	/**
	 * Get an instance of a class based on a class identifier only creating it if it doesn't exist yet.
	 *
	 * @param	string|object	$identifier The class identifier or identifier object
	 * @param	array  			$config     An optional associative array of configuration settings.
	 * @throws	RuntimeException if the service container has not been defined.
	 * @return	object  		Return object on success, throws exception on failure
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
	 * Gets the service identifier.
	 *
     * @param   null|KObjectIdentifier|string $identifier Identifier
	 * @return	KObjectIdentifier
     *
	 * @see 	KObjectInterface
     * @throws	RuntimeException if the service container has not been defined.
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
		else  $result = $this->__service_identifier;

	    return $result;
	}

	/**
     * Preform a deep clone of the object.
     *
     * @return void
     */
    public function __clone()
    {
        foreach($this->_mixed_methods as $method => $object) {
            $this->_mixed_methods[$method] = clone $object;
        }
    }

    /**
     * Search the mixin method map and call the method or trigger an error
     *
     * @param  string   $method    The function name
     * @param  array    $arguments The function arguments
     * @throws BadMethodCallException   If method could not be found
     * @return mixed The result of the function
     */
    public function __call($method, $arguments)
    {
        if(isset($this->_mixed_methods[$method]))
        {
            $object = $this->_mixed_methods[$method];
            $result = null;

            //Switch the mixin's attached mixer
            $object->setMixer($this);

            // Call_user_func_array is ~3 times slower than direct method calls.
            switch(count($arguments))
            {
                case 0 :
                    $result = $object->$method();
                    break;
                case 1 :
                    $result = $object->$method($arguments[0]);
                    break;
                case 2:
                    $result = $object->$method($arguments[0], $arguments[1]);
                    break;
                case 3:
                    $result = $object->$method($arguments[0], $arguments[1], $arguments[2]);
                    break;
                default:
                    // Resort to using call_user_func_array for many segments
                    $result = call_user_func_array(array($object, $method), $arguments);
             }

            return $result;
        }

        throw new BadMethodCallException('Call to undefined method :'.$method);
    }
}

<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright	Copyright (C) 2007 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/koowa for the canonical source repository
 */

/**
 * Object manager
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Koowa\Library\Object
 */
class KObjectManager implements KObjectInterface, KObjectManagerInterface
{
	/**
     * The identifier registry
     *
     * @var array
     */
    protected $_identifiers = null;

    /**
	 * The identifier aliases
	 *
	 * @var	array
	 */
	protected $_aliases = array();

	/**
	 * The objects
	 *
	 * @var	array
	 */
	protected $_objects = null;

	/**
	 * The mixins
	 *
	 * @var	array
	 */
	protected $_mixins = array();

    /**
     * The decorators
     *
     * @var	array
     */
    protected $_decorators = array();

	/**
	 * The configs
	 *
	 * @var	array
	 */
	protected $_configs = array();

    /*
     * The class loader
     *
     * @var KClassLoader
     */
    protected $_loader;

	/**
	 * Constructor
	 *
	 * Prevent creating instances of this class by making the constructor private
	 */
	final private function __construct(KObjectConfig $config)
	{
        // Set the class loader
        if (!$config->class_loader instanceof KClassLoaderInterface)
        {
            throw new InvalidArgumentException(
                'class_loader [KClassLoaderInterface] config option is required, "'.gettype($config->class_loader).'" given.'
            );
        }

        //Set the class loader
        $this->setClassLoader($config['class_loader']);

        // Create the object container
        $this->_objects = new ArrayObject();

        // Create the identifier registry
        $this->_identifiers = new KObjectIdentifierRegistry();

	    if ($config->cache_prefix) {
            $this->_identifiers->setCachePrefix($config->cache_prefix);
        }

	    if ($config->cache_enabled) {
            $this->_identifiers->enableCache($config->cache_enabled);
        }

	    //Auto-load the koowa adapter
        KObjectIdentifier::addLocator(new KObjectLocatorKoowa($config));

        //Register self and set a 'manager' alias
        $this->setObject($this->getIdentifier(), $this);
        $this->registerAlias('manager', 'koowa:object.manager');
	}

    /**
     * Initializes the options for the object
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param   KObjectConfig $config An optional ObjectConfig object with configuration options
     * @return  void
     */
    protected function _initialize(KObjectConfig $config)
    {
        $config->append(array(
            'class_loader'  => null,
            'cache_enabled' => false,
            'cache_prefix'  => 'koowa-cache-identifier'
        ));
    }

	/**
	 * Prevent creating clones of this class
     *
     * @throws Exception
	 */
	final private function __clone()
    {
        throw new Exception("An instance of KObjectManager cannot be cloned.");
    }

	/**
     * Force creation of a singleton
     *
     * @param  array  $config An optional array with configuration options.
     * @return KObjectManager
     */
	final public static function getInstance($config = array())
	{
		static $instance;

		if ($instance === NULL)
		{
			if(!$config instanceof KObjectConfig) {
				$config = new KObjectConfig($config);
			}

			$instance = new self($config);
		}

		return $instance;
	}

    /**
     * Returns an identifier object.
     *
     * Accepts various types of parameters and returns a valid identifier. Parameters can either be an
     * object that implements KObjectInterface, or a KObjectIdentifier object, or valid identifier
     * string. Function recursively resolves identifier aliases and returns the aliased identifier.
     *
     * If no identifier is passed the object identifier of this object will be returned.
     *
     * @param mixed $identifier An KObjectIdentifier, identifier string or object implementing KObjectInterface
     * @return KObjectIdentifier
     * @throws KObjectExceptionInvalidIdentifier If the identifier is not valid
     */
    public function getIdentifier($identifier = null)
    {
        if(isset($identifier))
        {
            if(!is_string($identifier))
            {
                if($identifier instanceof KObjectInterface) {
                    $identifier = $identifier->getIdentifier();
                }
            }

            //Recursively resolve the real identifier if an alias was passed
            while(array_key_exists((string) $identifier, $this->_aliases)) {
                $identifier = $this->_aliases[(string) $identifier];
            }

            if(!$this->_identifiers->offsetExists((string) $identifier))
            {
                if(is_string($identifier)) {
                    $identifier = new KObjectIdentifier($identifier);
                }

                $this->_identifiers->offsetSet((string) $identifier, $identifier);
            }
            else $identifier = $this->_identifiers->offsetGet((string)$identifier);
        }
        else $identifier = $this->getIdentifier('koowa:object.manager');

        return $identifier;
    }

    /**
     * Get an object instance based on an object identifier
     *
     * If the object implements the ObjectInstantiable interface the manager will delegate object instantiation
     * to the object itself.
     *
     * @param   mixed $identifier An KObjectIdentifier, identifier string or object implementing KObjectInterface
     * @param	array $config     An optional associative array of configuration settings
     * @return	KObjectInterface  Return object on success, throws exception on failure
     * @throws  KObjectExceptionInvalidIdentifier If the identifier is not valid
     * @throws	KObjectExceptionInvalidObject	  If the object doesn't implement the KObjectInterface
     * @throws  KObjectExceptionNotFound          If object cannot be loaded
     * @throws  KObjectExceptionNotInstantiated   If object cannot be instantiated
     */
    public function getObject($identifier, array $config = array())
	{
		$identifier = $this->getIdentifier($identifier);

		if(!$this->_objects->offsetExists((string) $identifier))
		{
		    //Instantiate the identifier
			$instance = $this->_instantiate($identifier, $config);

			//Perform the mixin
			$instance = $this->_mixin($identifier, $instance);

            //Decorate the object
            $instance = $this->_decorate($identifier, $instance);
		}
		else $instance = $this->_objects->offsetGet((string) $identifier);

		return $instance;
	}

	/**
	 * Insert the object instance using the identifier
	 *
	 * @param mixed $identifier An KObjectIdentifier, identifier string or object implementing KObjectInterface
	 * @param object $object    The object instance to store
     * @throws KObjectExceptionInvalidIdentifier If the identifier is not valid
     * @return void
	 */
	public function setObject($identifier, $object)
	{
		$objIdentifier = $this->getIdentifier($identifier);
		$strIdentifier = (string) $objIdentifier;

		$this->_objects->offsetSet($strIdentifier, $object);
	}

    /**
     * Register a mixin for an identifier
     *
     * The mixin is mixed when the identified object is first instantiated see {@link get} The mixin is also mixed with
     * with the represented by the identifier if the object is registered in the object manager. This mostly applies to
     * singletons but can also apply to other objects that are manually registered.
     *
     * @param mixed $identifier An KObjectIdentifier, identifier string or object implementing KObjectInterface
     * @param mixed $mixin      An KObjectIdentifier, identifier string or object implementing KObjectMixinInterface
     * @param array $config     Configuration for the mixin
     * @return KObjectManager
     * @throws KObjectExceptionInvalidIdentifier If the identifier is not valid
     * @see KObjectMixable::mixin()
     */
    public function registerMixin($identifier, $mixin, $config = array())
    {
        $identifier = $this->getIdentifier($identifier);

        if(!isset($this->_mixins[(string) $identifier])) {
            $this->_mixins[(string) $identifier] = array();
        }

        if ($mixin instanceof KObjectMixinInterface || $mixin instanceof KObjectIdentifier) {
            $this->_mixins[(string) $identifier][] = $mixin;
        } else {
            $this->_mixins[(string) $identifier][$mixin] = $config;
        }

        //If the identifier already exists mixin the mixin
        if ($this->isRegistered($identifier))
        {
            $mixer = $this->_objects->offsetGet((string)$identifier);
            $this->_mixin($identifier, $mixer);
        }

        return $this;
    }

    /**
     * Get the mixins for an identifier
     *
     * @param mixed $identifier An KObjectIdentifier, identifier string or object implementing KObjectInterface
     * @return array An array of mixins registered for the identifier
     * @throws KObjectExceptionInvalidIdentifier If the identifier is not valid
     */
    public function getMixins($identifier)
    {
        $objIdentifier = $this->getIdentifier($identifier);
        $strIdentifier = (string) $objIdentifier;

        return isset($this->_mixins[$strIdentifier])  ? $this->_mixins[$strIdentifier] : array();
    }

    /**
     * Register a decorator  for an identifier
     *
     * The object is decorated when it's first instantiated see {@link get} The object represented by the identifier is
     * also decorated if the object is registered in the object manager. This mostly applies to singletons but can also
     * apply to other objects that are manually registered.
     *
     * @param mixed $identifier An KObjectIdentifier, identifier string or object implementing KObjectInterface
     * @param mixed $decorator  An KObjectIdentifier, identifier string or object implementing KObjectDecoratorInterface
     * @param array $config     Configuration for the decorator
     * @return KObjectManager
     * @throws KObjectExceptionInvalidIdentifier If the identifier is not valid
     * @see KObjectDecoratable::decorate()
     */
    public function registerDecorator($identifier, $decorator, $config = array())
    {
        $identifier = $this->getIdentifier($identifier);

        if(!isset($this->_decorators[(string) $identifier])) {
            $this->_decorators[(string) $identifier] = array();
        }

        if ($decorator instanceof KObjectDecoratorInterface || $decorator instanceof KObjectIdentifier) {
            $this->_decorators[(string) $identifier][] = $decorator;
        } else {
            $this->_decorators[(string) $identifier][$decorator] = $config;
        }

        //If the identifier already exists decorate it
        if ($this->isRegistered($identifier))
        {
            $delegate = $this->_objects->offsetGet((string)$identifier);
            $this->_decorate($identifier, $delegate);
        }

        return $this;
    }

    /**
     * Get the decorators for an identifier
     *
     * @param mixed $identifier An KObjectIdentifier, identifier string or object implementing KObjectInterface
     * @return array An array of decorators registered for the identifier
     * @throws KObjectExceptionInvalidIdentifier If the identifier is not valid
     */
    public function getDecorators($identifier)
    {
        $objIdentifier = $this->getIdentifier($identifier);
        $strIdentifier = (string) $objIdentifier;

        return isset($this->_decorators[$strIdentifier])  ? $this->_decorators[$strIdentifier] : array();
    }

	/**
	 * Set an alias for an identifier
	 *
	 * @param string $alias     The alias
	 * @param mixed $identifier An KObjectIdentifier, identifier string or object implementing KObjectInterface
     * @return KObjectManager
     * @throws KObjectExceptionInvalidIdentifier If the identifier is not valid
	 */
	public function registerAlias($alias, $identifier)
	{
		//Don't register an alias if alias and identifier are the same
        if($alias != $identifier)
        {
            $identifier = $this->getIdentifier($identifier);
            $this->_aliases[$alias] = $identifier;
        }

        return $this;
	}

    /**
     * Get the aliases for an identifier
     *
     * @param mixed $identifier An KObjectIdentifier, identifier string or object implementing KObjectInterface
     * @return array   An array of aliases
     * @throws KObjectExceptionInvalidIdentifier If the identifier is not valid
     */
    public function getAliases($identifier)
    {
        $identifier = $this->getIdentifier($identifier);
        return array_search((string) $identifier, $this->_aliases);
    }

    /**
     * Get the class loader
     *
     * @return KClassLoaderInterface
     */
    public function getClassLoader()
    {
        return $this->_loader;
    }

    /**
     * Set the class loader
     *
     * @param  KClassLoaderInterface $loader
     * @return KObjectManagerInterface
     */
    public function setClassLoader(KClassLoaderInterface $loader)
    {
        $this->_loader = $loader;
        return $this;
    }

	/**
	 * Set the configuration options for an identifier
	 *
	 * @param mixed $identifier An KObjectIdentifier, identifier string or object implementing KObjectInterface
	 * @param array	$config     An associative array of configuration options
     * @return KObjectManager
     * @throws KObjectExceptionInvalidIdentifier If the identifier is not valid
	 */
	public function setConfig($identifier, array $config)
	{
		$objIdentifier = $this->getIdentifier($identifier);
		$strIdentifier = (string) $objIdentifier;

		if(isset($this->_configs[$strIdentifier])) {
		    $this->_configs[$strIdentifier] =  array_merge_recursive($this->_configs[$strIdentifier], $config);
		} else {
		    $this->_configs[$strIdentifier] = $config;
		}

        return $this;
	}

	/**
	 * Get the configuration options for an identifier
	 *
	 * @param  mixed $identifier An KObjectIdentifier, identifier string or object implementing KObjectInterface
	 * @return array An associative array of configuration options
     * @throws KObjectExceptionInvalidIdentifier If the identifier is not valid
	 */
	public function getConfig($identifier)
	{
		$objIdentifier = $this->getIdentifier($identifier);
		$strIdentifier = (string) $objIdentifier;

	    return isset($this->_configs[$strIdentifier])  ? $this->_configs[$strIdentifier] : array();
	}

    /**
     * Check if the object instance exists based on the identifier
     *
     * @param  mixed $identifier An KObjectIdentifier, identifier string or object implementing KObjectInterface
     * @return boolean Returns TRUE on success or FALSE on failure.
     * @throws KObjectExceptionInvalidIdentifier If the identifier is not valid
     */
    public function isRegistered($identifier)
    {
        try
        {
            $objIdentifier = $this->getIdentifier($identifier);
            $strIdentifier = (string) $objIdentifier;
            $result = (bool) $this->_objects->offsetExists($strIdentifier);

        } catch (KObjectExceptionInvalidIdentifier $e) {
            $result = false;
        }

        return $result;
    }

    /**
     * Perform the actual mixin of all registered mixins for an object
     *
     * @param  KObjectIdentifier $identifier
     * @param  KObjectMixable    $mixer
     * @return KObjectMixable    The mixed object
     */
    protected function _mixin(KObjectIdentifier $identifier, $mixer)
    {
        if(isset($this->_mixins[(string) $identifier]))
        {
            $mixins = $this->_mixins[(string) $identifier];

            foreach ($mixins as $key => $value)
            {
                if (is_numeric($key)) {
                    $mixer->mixin($value);
                } else {
                    $mixer->mixin($key, $value);
                }
            }
        }

        return $mixer;
    }

    /**
     * Perform the actual decoration of all registered decorators for an object
     *
     * @param  KObjectIdentifier  $identifier
     * @param  KObjectDecoratable $delegate
     * @return KObjectDecorator  The decorated object
     */
    protected function _decorate(KObjectIdentifier $identifier, $delegate)
    {
        if(isset($this->_decorators[(string) $identifier]))
        {
            $decorators = $this->_decorators[(string) $identifier];

            foreach ($decorators as $key => $value)
            {
                if (is_numeric($key)) {
                    $delegate = $delegate->decorate($value);
                } else {
                    $delegate = $delegate->decorate($key, $value);
                }
            }
        }

        return $delegate;
    }

    /**
     * Configure an identifier
     *
     * @param KObjectIdentifier $identifier
     * @param array             $config
     * @return KObjectConfig
     */
    protected function _configure(KObjectIdentifier $identifier, array $config = array())
    {
        //Create the configuration object
        $config = new KObjectConfig(array_merge($this->getConfig($identifier), $config));

        //Set the object manager and identifier
        $config->object_manager  = $this;
        $config->object_identifier = $identifier;

        return $config;
    }

    /**
     * Get an instance of a class based on a class identifier
     *
     * @param   KObjectIdentifier $identifier
     * @param   array              $config      An optional associative array of configuration settings.
     * @return  object  Return object on success, throws exception on failure
     * @throws	KObjectExceptionInvalidObject	  If the object doesn't implement the KObjectInterface
     * @throws  KObjectExceptionNotFound          If object cannot be loaded
     * @throws  KObjectExceptionNotInstantiated   If object cannot be instantiated
     */
    protected function _instantiate(KObjectIdentifier $identifier, array $config = array())
    {
        $result = null;

        //Load the class manually using the basepath
        if($this->getClassLoader()->loadClass($identifier->classname, $identifier->basepath))
        {
            if (!array_key_exists('KObjectInterface', class_implements($identifier->classname, false)))
            {
                throw new KObjectExceptionInvalidObject(
                    'Object: '.$identifier->classname.' does not implement KObjectInterface'
                );
            }

            //Configure the identifier
            $config = $this->_configure($identifier, $config);

            // Delegate object instantiation.
            if (array_key_exists('KObjectInstantiable', class_implements($identifier->classname, false))) {
                $result = call_user_func(array($identifier->classname, 'getInstance'), $config, $this);
            } else {
                $result = new $identifier->classname($config);
            }

            //Thrown an error if no object was instantiated
            if (!is_object($result))
            {
                throw new KObjectExceptionNotInstantiated(
                    'Cannot instantiate object from identifier: ' . $identifier->classname
                );
            }
        }
        else {
            throw new KObjectExceptionNotFound('Cannot load object from identifier: '. $identifier);
        }

        return $result;
    }
}

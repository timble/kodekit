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
class KObjectManager implements KObjectManagerInterface
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
        else $this->setClassLoader($config['class_loader']);

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
        KObjectIdentifier::addLocator(new KObjectLocatorKoowa());
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
	public static function getInstance($config = array())
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
	 * Get an instance of a class based on a class identifier only creating it
	 * if it doesn't exist yet.
	 *
	 * @param	mixed	$identifier An object that implements KObjectInterface, KObjectIdentifier object
	 * 					            or valid identifier string
	 * @param	array   $config     An optional associative array of configuration settings.
	 * @return	object  Return object on success, throws exception on failure
	 */
	public function getObject($identifier, array $config = array())
	{
		$objIdentifier = self::getIdentifier($identifier);
		$strIdentifier = (string) $objIdentifier;

		if(!$this->_objects->offsetExists($strIdentifier))
		{
		    //Instantiate the identifier
			$instance = self::_instantiate($objIdentifier, $config);

			//Perform the mixin
			self::_mixin($strIdentifier, $instance);
		}
		else $instance = $this->_objects->offsetGet($strIdentifier);

		return $instance;
	}

	/**
	 * Insert the object instance using the identifier
	 *
	 * @param mixed $identifier An object that implements KObjectInterface, KObjectIdentifier object
	 * 					        or valid identifier string
	 * @param object $object    The object instance to store
	 */
	public function setObject($identifier, $object)
	{
		$objIdentifier = self::getIdentifier($identifier);
		$strIdentifier = (string) $objIdentifier;

		$this->_objects->offsetSet($strIdentifier, $object);
	}

	/**
	 * Check if the object instance exists based on the identifier
	 *
	 * @param	mixed	$identifier An object that implements KObjectInterface, KObjectIdentifier object
	 * 					            or valid identifier string
	 * @return boolean Returns TRUE on success or FALSE on failure.
	 */
	public function isRegistered($identifier)
	{
		try
		{
	        $objIdentifier = self::getIdentifier($identifier);
	        $strIdentifier = (string) $objIdentifier;
	        $result = (bool) $this->_objects->offsetExists($strIdentifier);

		} catch (KObjectIdentifierException $e) {
		    $result = false;
		}

		return $result;
	}

	/**
     * Add a mixin or an array of mixins for an identifier
     *
     * The mixins are mixed when the identified object is first instantiated see {@link get} Mixins are also added to
     * objects that already exist in the object manager.
     *
     * @param	mixed	$identifier An object that implements KObjectInterface, KObjectIdentifier object
	 * 					            or valid identifier string
     * @param  string|array $mixins A mixin identifier or a array of mixin identifiers
     * @see KObject::mixin
     */
    public function registerMixin($identifier, $mixins)
    {
        settype($mixins, 'array');

        $objIdentifier = self::getIdentifier($identifier);
        $strIdentifier = (string) $objIdentifier;

        if (!isset($this->_mixins[$strIdentifier]) ) {
            $this->_mixins[$strIdentifier] = array();
        }

        $this->_mixins[$strIdentifier] = array_unique(array_merge($this->_mixins[$strIdentifier], $mixins));

        if($this->_objects->offsetExists($strIdentifier))
        {
            $instance = $this->_objects->offsetGet($strIdentifier);
            self::_mixin($strIdentifier, $instance);
        }
    }

    /**
     * Get the mixins for an identifier
     *
     * @param	mixed	$identifier An object that implements KObjectInterface, KObjectIdentifier object
	 * 					            or valid identifier string
     * @return array 	An array of mixins
     */
    public function getMixins($identifier)
    {
        $objIdentifier = self::getIdentifier($identifier);
        $strIdentifier = (string) $objIdentifier;

        $result = array();
        if(isset($this->_mixins[$strIdentifier])) {
            $result = $this->_mixins[$strIdentifier];
        }

        return $result;
    }

	/**
	 * Returns an identifier object.
	 *
	 * Accepts various types of parameters and returns a valid identifier. Parameters can either be an
	 * object that implements KObjectInterface, or a KObjectIdentifier object, or valid identifier
	 * string. Function will also check for identifier mappings and return the mapped identifier.
	 *
	 * @param	mixed	$identifier An object that implements KObjectInterface, KObjectIdentifier object
	 * 					            or valid identifier string
	 * @return KObjectIdentifier
	 */
	public function getIdentifier($identifier)
	{
	    if(!is_string($identifier))
		{
	        if($identifier instanceof KObjectInterface) {
			    $identifier = $identifier->getIdentifier();
		    }
		}

	    $alias = (string) $identifier;
	    if(array_key_exists($alias, $this->_aliases)) {
	        $identifier = $this->_aliases[$alias];
		}

	    if(!$this->_identifiers->offsetExists((string) $identifier))
        {
		    if(is_string($identifier)) {
		        $identifier = new KObjectIdentifier($identifier);
		    }

		    $this->_identifiers->offsetSet((string) $identifier, $identifier);
        }
        else $identifier = $this->_identifiers->offsetGet((string)$identifier);

		return $identifier;
	}

	/**
	 * Set an alias for an identifier
	 *
	 * @param string  $alias        The alias
	 * @param mixed	  $identifier   An object that implements KObjectInterface, KObjectIdentifier object
	 * 				                or valid identifier string
	 */
	public function registerAlias($alias, $identifier)
	{
		$identifier = self::getIdentifier($identifier);

		$this->_aliases[$alias] = $identifier;
	}

	/**
	 * Get an alias for an identifier
	 *
	 * @param  string  $alias The alias
	 * @return mixed   The class identifier or identifier object, or NULL if no alias was found.
	 */
	public function getAlias($alias)
	{
		return isset($this->_aliases[$alias])  ? $this->_aliases[$alias] : null;
	}

	/**
     * Get a list of aliases
     *
     * @return array
     */
    public function getAliases()
    {
        return $this->_aliases;
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
	 * @param mixed	  $identifier An object that implements KObjectInterface, KObjectIdentifier object
	 * 				              or valid identifier string
	 * @param array	  $config     An associative array of configuration options
	 */
	public function setConfig($identifier, array $config)
	{
		$objIdentifier = self::getIdentifier($identifier);
		$strIdentifier = (string) $objIdentifier;

		if(isset($this->_configs[$strIdentifier])) {
		    $this->_configs[$strIdentifier] =  array_merge_recursive($this->_configs[$strIdentifier], $config);
		} else {
		    $this->_configs[$strIdentifier] = $config;
		}
	}

	/**
	 * Get the configuration options for an identifier
	 *
	 * @param mixed	  $identifier An object that implements KObjectInterface, KObjectIdentifier object
	 * 				             or valid identifier string
	 * @return array  An associative array of configuration options
	 */
	public function getConfig($identifier)
	{
		$objIdentifier = self::getIdentifier($identifier);
		$strIdentifier = (string) $objIdentifier;

	    return isset($this->_configs[$strIdentifier])  ? $this->_configs[$strIdentifier] : array();
	}

	/**
     * Get the configuration options for all the identifiers
     *
     * @return array  An associative array of configuration options
     */
    public function getConfigs()
    {
        return $this->_configs;
    }

    /**
     * Perform the actual mixin of all registered mixins with an object
     *
     * @param	mixed	$identifier An object that implements KObjectInterface, KObjectIdentifier object
	 * 					            or valid identifier string
     * @param   object  $instance   A KObject instance to used as the mixer
     * @return void
     */
    protected function _mixin($identifier, $instance)
    {
        if(isset($this->_mixins[$identifier]) && $instance instanceof KObject)
        {
            $mixins = $this->_mixins[$identifier];
            foreach($mixins as $mixin)
            {
                $mixin = self::getObject($mixin, array('mixer'=> $instance));
                $instance->mixin($mixin);
            }
        }
    }

    /**
     * Get an instance of a class based on a class identifier
     *
     * @param   KObjectIdentifier $identifier	A KObjectIdentifier object
     * @param   array              $config      An optional associative array of configuration settings.
     * @return  object  Return object on success, throws exception on failure
     * @throws  UnexpectedValueException
     */
    protected function _instantiate(KObjectIdentifier $identifier, array $config = array())
    {
        $result = null;

        //Load the class manually using the basepath
        if($this->getClassLoader()->loadClass($identifier->classname, $identifier->basepath))
        {
            if(array_key_exists('KObjectInterface', class_implements($identifier->classname)))
            {
                //Create the configuration object
                $config = new KObjectConfig(array_merge(self::getConfig($identifier), $config));

                //Set the object manager and identifier
                $config->object_manager  = self::getInstance();
                $config->object_identifier = $identifier;

                // If the class has an instantiate method call it
                if(array_key_exists('KObjectInstantiatable', class_implements($identifier->classname))) {
                    $result = call_user_func(array($identifier->classname, 'getInstance'), $config, self::getInstance());
                } else {
                    $result = new $identifier->classname($config);
                }

            }
        }

        //Thrown an error if no object was instantiated
        if(!is_object($result)) {
            throw new UnexpectedValueException('Cannot instantiate object from identifier : '.$identifier);
        }

        return $result;
    }
}

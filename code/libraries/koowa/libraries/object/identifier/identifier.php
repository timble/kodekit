<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright	Copyright (C) 2007 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/koowa for the canonical source repository
 */

/**
 * Object Identifier
 *
 * Wraps identifiers of the form type:[//application/]package.[.path].name in an object, providing public accessors and
 * methods for derived formats.
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Koowa\Library\Object
 *
 * @property string $name object name
 */
class KObjectIdentifier implements KObjectIdentifierInterface
{
    /**
     * An associative array of application paths
     *
     * @var array
     */
    protected static $_applications = array();

    /**
     * An associative array of package paths
     *
     * @var array
     */
    protected static $_packages = array();

    /**
     * Associative array of identifier adapters
     *
     * @var array
     */
    protected static $_locators = array();

    /**
     * The identifier
     *
     * @var string
     */
    protected $_identifier = '';

    /**
     * The application name
     *
     * @var string
     */
    protected $_application = '';

    /**
     * The identifier type [com|plg|mod]
     *
     * @var string
     */
    protected $_type = '';

    /**
     * The identifier package
     *
     * @var string
     */
    protected $_package = '';

    /**
     * The identifier path
     *
     * @var array
     */
    protected $_path = array();

    /**
     * The identifier object name
     *
     * @var string
     */
    protected $_name = '';

     /**
     * The classname
     *
     * @var string
     */
    protected $_classname = '';

    /**
     * The base path
     *
     * @var string
     */
    protected $_basepath = '';

    /**
     * Constructor
     *
     * @param   string $identifier Identifier string or object in type://namespace/package.[.path].name format
     * @throws  KObjectExceptionInvalidIdentifier If the identifier cannot be parsed
     */
    public function __construct($identifier)
    {
        //Get the parts
        if(false === $parts = parse_url($identifier)) {
            throw new KObjectExceptionInvalidIdentifier('Identifier cannot be parsed : '.$identifier);
        }

        // Set the type
        $this->type = isset($parts['scheme']) ? $parts['scheme'] : 'koowa';

        //Set the application
        if(isset($parts['host'])) {
            $this->application = $parts['host'];
        }

        // Set the path
        $this->_path = trim($parts['path'], '/');
        $this->_path = explode('.', $this->_path);

        // Set the extension (first part)
        $this->package = array_shift($this->_path);

        // Set the name (last part)
        if(count($this->_path)) {
            $this->_name = array_pop($this->_path);
        }

        //Cache the identifier to increase performance
        $this->_identifier = $identifier;
    }

	/**
	 * Serialize the identifier
	 *
	 * @return string 	The serialised identifier
	 */
	public function serialize()
	{
        $data = array(
            'application' => $this->_application,
            'type'		  => $this->_type,
            'package'	  => $this->_package,
            'path'		  => $this->_path,
            'name'		  => $this->_name,
            'identifier'  => $this->_identifier,
            'basepath'    => $this->_basepath,
            'classname'   => $this->classname,
        );

        return serialize($data);
	}

	/**
	 * Unserialize the identifier
	 *
	 * @param string 	$data The serialised identifier
	 */
	public function unserialize($data)
	{
	    $data = unserialize($data);

	    foreach($data as $property => $value) {
	        $this->{'_'.$property} = $value;
	    }
	}

    /**
     * Checks if the identifier extends a class, implements an interface or uses a trait
     *
     * @param string  $class An identifier object or a class name
     * @param boolean $autoload  Whether to allow this function to load the class automatically through the __autoload()
     *                           magic method.
     * @return bool
     */
    public function inherits($class, $autoload = true)
    {
        if($class instanceof KObjectIdentifier) {
            $class = $class->classname;
        }

        //Check parent classes
        if(array_key_exists($class, class_parents($this->classname, $autoload))) {
            return true;
        }

        //Check interfaces
        if(array_key_exists($class, class_implements($this->classname, $autoload))) {
            return true;
        }

        //Check traits
        if(function_exists('class_uses'))
        {
            if(array_key_exists($class, class_uses($this->classname, $autoload))) {
                return true;
            }
        }

        return false;
    }

    /**
     * Checks if the identifier has been defined
     *
     * @return bool Returns TRUE if the identifier exists, FALSE otherwise.
     */
    public function exists()
    {
        return (bool) $this->getLocator()->locate($this, false);
    }

    /**
     * Get the identifier type
     *
     * @return string
     */
    public function getType()
    {
        return $this->_type;
    }

    /**
     * Set the identifier type
     *
     * @param  string $type
     * @return  KObjectIdentifierInterface
     */
    public function setType($type)
    {
        $this->type = $type;
        return $this;
    }

    /**
     * Get the identifier package
     *
     * @return string
     */
    public function getPackage()
    {
        return $this->_package;
    }

    /**
     * Set the identifier package
     *
     * @param  string $package
     * @return  KObjectIdentifierInterface
     */
    public function setPackage($package)
    {
        $this->package = $package;
        return $this;
    }

    /**
     * Get the identifier package
     *
     * @return array
     */
    public function getPath()
    {
        return $this->_path;
    }

    /**
     * Set the identifier path
     *
     * @param  string $path
     * @return  KObjectIdentifierInterface
     */
    public function setPath(array $path)
    {
        $this->_path = $path;
        return $this;
    }

    /**
     * Get the identifier package
     *
     * @return string
     */
    public function getName()
    {
        return $this->_name;
    }

    /**
     * Set the identifier name
     *
     * @param  string $name
     * @return  KObjectIdentifierInterface
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

	/**
	 * Set an application path
	 *
	 * @param string $application The name of the application
	 * @param string $path        The path of the application
	 * @return void
     */
    public static function registerApplication($application, $path)
    {
        self::$_applications[$application] = $path;
    }

	/**
	 * Get an application path
	 *
	 * @param string    $application   The name of the application
	 * @return string	The path of the application
     */
    public static function getApplicationPath($application)
    {
        return isset(self::$_applications[$application]) ? self::$_applications[$application] : null;
    }

	/**
     * Get a list of applications
     *
     * @return array
     */
    public static function getApplications()
    {
        return self::$_applications;
    }

    /**
     * Set a package path
     *
     * @param string $package    The name of the package
     * @param string $path       The path of the package
     * @return void
     */
    public static function registerPackage($package, $path)
    {
        self::$_packages[$package] = $path;
    }

    /**
     * Get a package path
     *
     * @param string    $package   The name of the application
     * @return string	The path of the application
     */
    public static function getPackagePath($package)
    {
        return isset(self::$_packages[$package]) ? self::$_packages[$package] : null;
    }

    /**
     * Get a list of packages
     *
     * @return array
     */
    public static function getPackages()
    {
        return self::$_packages;
    }

	/**
     * Add a identifier adapter
     *
     * @param KObjectLocatorInterface $locator A KObjectLocator
     * @return void
     */
    public static function addLocator(KObjectLocatorInterface $locator)
    {
        self::$_locators[$locator->getType()] = $locator;
    }

    /**
     * Get the object locator
     *
     * @return KObjectLocatorInterface|null  Returns the object locator or NULL if the locator can not be found.
     */
    public function getLocator()
    {
        $result = null;
        if(isset(self::$_locators[$this->_type])) {
            $result = self::$_locators[$this->_type];
        }

        return $result;
    }

	/**
     * Get the registered locators
     *
     * @return array
     */
    public static function getLocators()
    {
        return self::$_locators;
    }

    /**
     * Get the identifier class name
     *
     * @return string
     */
    public function getClassName()
    {
        return $this->classname;
    }

    /**
     * Check if the object is a multiton
     *
     * @return boolean Returns TRUE if the object is a singleton, FALSE otherwise.
     */
    public function isMultiton()
    {
        return array_key_exists('KObjectMultiton', class_implements($this->classname));
    }

    /**
     * Check if the object is a singleton
     *
     * @return boolean Returns TRUE if the object is a singleton, FALSE otherwise.
     */
    public function isSingleton()
    {
        return array_key_exists('KObjectSingleton', class_implements($this->classname));
    }

    /**
     * Formats the identifier as a type:[//application/]package.[.path].name string
     *
     * @return string
     */
    public function toString()
    {
        if($this->_identifier == '')
        {
            if(!empty($this->_type)) {
                $this->_identifier .= $this->_type;
            }

            if(!empty($this->_application)) {
                $this->_identifier .= '://'.$this->_application.'/';
            } else {
                $this->_identifier .= ':';
            }

            if(!empty($this->_package)) {
                $this->_identifier .= $this->_package;
            }

            if(count($this->_path)) {
                $this->_identifier .= '.'.implode('.',$this->_path);
            }

            if(!empty($this->_name)) {
                $this->_identifier .= '.'.$this->_name;
            }
        }

        return $this->_identifier;
    }

    /**
     * Implements the virtual class properties
     *
     * This function creates a string representation of the identifier.
     *
     * @param   string $property The virtual property to set.
     * @param   string $value    Set the virtual property to this value.
     * @throws  KObjectExceptionInvalidIdentifier If the application or type are unknown
     */
    public function __set($property, $value)
    {
        if (property_exists($this, '_'.$property))
        {
            //Force the path to an array
            if($property == 'path')
            {
                if(is_scalar($value)) {
                    $value = (array) $value;
                }
            }

            //Set the base path based on the application path
            if($property == 'application')
            {
                //Check if the application is registered
                if(!isset(self::$_applications[$value])) {
                    throw new KObjectExceptionInvalidIdentifier('Unknown application: '.$value);
               }

               $this->_basepath = self::$_applications[$value];
            }

            if($property == 'package')
            {
                if(isset(self::$_packages[$value])) {
                    $this->_basepath = self::$_packages[$value];
                }
            }

            //Set the type and make sure it's
            if($property == 'type')
            {
                //Check if the type is registered
                if($value != 'koowa' && !isset(self::$_locators[$value]))  {
                    throw new KObjectExceptionInvalidIdentifier('Unknown type: '.$value);
                }
            }


            //Set the properties
            $this->{'_'.$property} = $value;

            //Unset the properties
            $this->_identifier = '';
            $this->_classname  = '';
        }
    }

    /**
     * Implements access to virtual properties by reference so that it appears to be a public property.
     *
     * @param   string  $property The virtual property to return.
     * @return  array   The value of the virtual property.
     */
    public function &__get($property)
    {
        $result = null;
        if(isset($this->{'_'.$property}))
        {
            if($property == 'classname' && empty($this->_classname)) {
                $this->_classname = self::$_locators[$this->_type]->locate($this);
            }

            $result =& $this->{'_'.$property};
        }

        return $result;
    }

    /**
     * This function checks if a virtual property is set.
     *
     * @param   string  $property The virtual property to return.
     * @return  boolean True if it exists otherwise false.
     */
    public function __isset($property)
    {
        return isset($this->{'_'.$property});
    }

    /**
     * Allow casting of the identfiier to a string
     *
     * @return string
     */
    public function __toString()
    {
        return $this->toString();
    }
}

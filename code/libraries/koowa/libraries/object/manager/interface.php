<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright	Copyright (C) 2007 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/koowa for the canonical source repository
 */

/**
 * Object Manager Interface
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Koowa\Library\Object
 */
interface KObjectManagerInterface
{
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
    //public function getIdentifier($identifier = null);

    /**
     * Get the identifier class
     *
     * @param mixed $identifier An KObjectIdentifier, identifier string or object implementing KObjectInterface
     * @param bool  $fallback   Use fallbacks when locating the class. Default is TRUE.
     * @return string|false  Returns the class name or false if the class could not be found.
     */
    public function getClass($identifier, $fallback = true);

    /**
     * Get the identifier class
     *
     * @param mixed  $identifier An KObjectIdentifier, identifier string or object implementing KObjectInterface
     * @param string $class      The class name
     * @return string
     */
    public function setClass($identifier, $class);

    /**
     * Get an object instance based on an object identifier
     *
     * If the object implements the ObjectInstantiable interface the manager will delegate object instantiation
     * to the object itself.
     *
     * @param   mixed $identifier An KObjectIdentifier, identifier string or object implementing KObjectInterface
     * @param	array $config     An optional associative array of configuration settings.
     * @return	KObjectInterface  Return object on success, throws exception on failure
     * @throws  KObjectExceptionInvalidIdentifier If the identifier is not valid
     * @throws	KObjectExceptionInvalidObject	  If the object doesn't implement the KObjectInterface
     * @throws  KObjectExceptionNotFound          If object cannot be loaded
     * @throws  KObjectExceptionNotInstantiated   If object cannot be instantiated
     */
    //public function getObject($identifier, array $config = array());

	/**
	 * Insert the object instance using the identifier
	 *
	 * @param mixed $identifier An KObjectIdentifier, identifier string or object implementing KObjectInterface
	 * @param object $object     The object instance to store
     * @return KObjectManagerInterface
     * @throws KObjectExceptionInvalidIdentifier If the identifier is not valid
	 */
	public function setObject($identifier, $object);

    /**
     * Get the configuration options for an identifier
     *
     * @param mixed $identifier An KObjectIdentifier, identifier string or object implementing KObjectInterface
     * @throws KObjectExceptionInvalidIdentifier If the identifier is not valid
     */
    public function getConfig($identifier);

    /**
     * Takes an object's identifier and public properties and serializes them
     *
     * @param $object  object
     * @param $to_array boolean Returns a raw array if true
     * @return string
     */
    public function serializeObject($object, $to_array = false);

    /**
     * Unserializes an object and injects the object manager
     *
     * @param $object object
     * @param $data   string serialized string
     * @throws UnexpectedValueException
     */
    public function unserializeObject($object, $data);

    /**
     * Set the configuration options for an identifier
     *
     * @param mixed $identifier An KObjectIdentifier, identifier string or object implementing KObjectInterface
     * @param array	$config     An associative array of configuration options
     * @param  boolean  $merge  If TRUE the data in $config will be merged instead of replaced. Default TRUE.
     * @return KObjectManager
     * @throws KObjectExceptionInvalidIdentifier If the identifier is not valid
     */
    public function setConfig($identifier, $config, $merge = true);

    /**
     * Register a mixin for an identifier
     *
     * The mixin is mixed when the identified object is first instantiated see {@link get} The mixin is also mixed with
     * with the represented by the identifier if the object is registered in the object manager. This mostly applies to
     * singletons but can also apply to other objects that are manually registered.
     *
     * @param mixed $identifier An KObjectIdentifier, identifier string or object implementing KObjectInterface
     * @param mixed $mixin      An KObjectIdentifier, identifier string or object implementing ObjectMixinInterface
     * @return KObjectManagerInterface
     * @throws KObjectExceptionInvalidIdentifier If the identifier is not valid
     * @see KObjectMixable::mixin()
     */
    public function registerMixin($identifier, $mixin);

    /**
     * Register a decorator for an identifier
     *
     * The object is decorated when it's first instantiated see {@link get} The object represented by the identifier is
     * also decorated if the object is registered in the object manager. This mostly applies to singletons but can also
     * apply to other objects that are manually registered.
     *
     * @param mixed $identifier An KObjectIdentifier, identifier string or object implementing KObjectInterface
     * @param mixed $decorator  An KObjectIdentifier, identifier string or object implementing KObjectDecoratorInterface
     * @return KObjectManagerInterface
     * @throws KObjectExceptionInvalidIdentifier If the identifier is not valid
     * @see KObjectDecoratable::decorate()
     */
    public function registerDecorator($identifier, $decorator);

    /**
     * Set an alias for an identifier
     *
     * @param mixed $identifier An KObjectIdentifier, identifier string or object implementing KObjectInterface
     * @param string $alias     The identifier alias
     * @return KObjectManager
     * @throws KObjectExceptionInvalidIdentifier If the identifier is not valid
     */
    public function registerAlias($identifier, $alias);

    /**
     * Get the aliases for an identifier
     *
     * @param mixed $identifier An KObjectIdentifier, identifier string or object implementing KObjectInterface
     * @return array An array of aliases
     * @throws KObjectExceptionInvalidIdentifier If the identifier is not valid
     */
    public function getAliases($identifier);

    /**
     * Register an object locator
     *
     * @param mixed $identifier An ObjectIdentifier, identifier string or object implementing ObjectLocatorInterface
     * @return KObjectManagerInterface
     * @throws KObjectExceptionInvalidIdentifier If the identifier is not valid
     */
    public function registerLocator($identifier, array $config = array());

    /**
     * Get a registered object locator based on his type
     *
     * @param string $type The locator type
     * @return KObjectLocatorInterface|null  Returns the object locator or NULL if it cannot be found.
     */
    public function getLocator($type);

    /**
     * Get the class loader
     *
     * @return KClassLoaderInterface
     */
    public function getClassLoader();

    /**
     * Set the class loader
     *
     * @param KClassLoaderInterface $loader
     * @return KObjectManagerInterface
     */
    public function setClassLoader(KClassLoaderInterface $loader);

    /**
     * Check if the object instance exists based on the identifier
     *
     * @param mixed $identifier An KObjectIdentifier, identifier string or object implementing KObjectInterface
     * @return boolean Returns TRUE on success or FALSE on failure.
     * @throws KObjectExceptionInvalidIdentifier If the identifier is not valid
     */
    public function isRegistered($identifier);

    /**
     * Check if the object is a multiton
     *
     * @param mixed $identifier An object that implements the ObjectInterface, an ObjectIdentifier or valid identifier string
     * @return boolean Returns TRUE if the object is a singleton, FALSE otherwise.
     */
    public function isMultiton($identifier);

    /**
     * Check if the object is a singleton
     *
     * @param mixed $identifier An object that implements the ObjectInterface, an ObjectIdentifier or valid identifier string
     * @return boolean Returns TRUE if the object is a singleton, FALSE otherwise.
     */
    public function isSingleton($identifier);
}

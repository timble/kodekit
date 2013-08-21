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
    public function getIdentifier($identifier = null);

    /**
     * Get an object instance based on an object identifier
     *
     * If the object implements the ObjectInstantiable interface the manager will delegate object instantiation
     * to the object itself.
     *
     * @@param mixed $identifier An KObjectIdentifier, identifier string or object implementing KObjectInterface
     * @param	array$config     An optional associative array of configuration settings.
     * @return	KObjectInterface  Return object on success, throws exception on failure
     * @throws  KObjectExceptionInvalidIdentifier If the identifier is not valid
     * @throws	KObjectExceptionInvalidObject	  If the object doesn't implement the KObjectInterface
     * @throws  KObjectExceptionNotFound          If object cannot be loaded
     * @throws  KObjectExceptionNotInstantiated   If object cannot be instantiated
     */
    public function getObject($identifier, array $config = array());

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
     * Set the configuration options for an identifier
     *
     * @param mixed $identifier An KObjectIdentifier, identifier string or object implementing KObjectInterface
     * @param array	 $config    An associative array of configuration options
     * @return KObjectManagerInterface
     * @throws KObjectExceptionInvalidIdentifier If the identifier is not valid
     */
    public function setConfig($identifier, array $config);

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
     * Get the mixins for an identifier
     *
     * @param mixed $identifier An object that implements the KObjectInterface, an KObjectIdentifier or valid identifier string
     * @return array An array of mixins registered for the identifier
     * @throws KObjectExceptionInvalidIdentifier If the identifier is not valid
     */
    public function getMixins($identifier);

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
     * Get the decorators for an identifier
     *
     * @param mixed $identifier An KObjectIdentifier, identifier string or object implementing KObjectInterface
     * @return array An array of decorators registered for the identifier
     * @throws KObjectExceptionInvalidIdentifier If the identifier is not valid
     */
    public function getDecorators($identifier);

    /**
     * Register an alias for an identifier
     *
     * @param string $alias      The alias
     * @param mixed  $identifier An KObjectIdentifier, identifier string or object implementing KObjectInterface
     * @return KObjectManagerInterface
     * @throws KObjectExceptionInvalidIdentifier If the identifier is not valid
     */
    public function registerAlias($alias, $identifier);

    /**
     * Get the aliases for an identifier
     *
     * @param mixed $identifier An KObjectIdentifier, identifier string or object implementing KObjectInterface
     * @return array An array of aliases
     * @throws KObjectExceptionInvalidIdentifier If the identifier is not valid
     */
    public function getAliases($identifier);

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
}

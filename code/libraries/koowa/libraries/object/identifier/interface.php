<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright	Copyright (C) 2007 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/koowa for the canonical source repository
 */

/**
 * Object Identifier Interface
 *
 * Wraps identifiers of the form type:[//application/]package.[.path].name in an object, providing public accessors and
 * methods for derived formats.
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Koowa\Library\Object
 */
interface KObjectIdentifierInterface extends Serializable
{
    /**
     * Checks if the identifier extends a class, implements an interface or uses a trait
     *
     * @param string $class An identifier object or a class name
     * @param boolean $autoload  Whether to allow this function to load the class automatically through the __autoload()
     *                           magic method.
     */
    public function inherits($class, $autoload = true);

    /**
     * Checks if the identifier has been defined
     *
     * @return bool Returns TRUE if the identifier exists, FALSE otherwise.
     */
    public function exists();

    /**
     * Get the identifier type
     *
     * @return string
     */
    public function getType();

    /**
     * Set the identifier type
     *
     * @param  string $type
     * @return KObjectIdentifierInterface
     * @throws DomainException If the type is unknown
     */
    public function setType($type);

    /**
     * Get the identifier package
     *
     * @return string
     */
    public function getPackage();

    /**
     * Set the identifier package
     *
     * @param  string $package
     * @return KObjectIdentifierInterface
     */
    public function setPackage($package);

    /**
     * Get the identifier package
     *
     * @return array
     */
    public function getPath();

    /**
     * Set the identifier path
     *
     * @param  string $path
     * @return KObjectIdentifierInterface
     */
    public function setPath(array $path);

    /**
     * Get the identifier package
     *
     * @return string
     */
    public function getName();

    /**
     * Set the identifier name
     *
     * @param  string $name
     * @return KObjectIdentifierInterface
     */
    public function setName($name);

    /**
     * Set an application path
     *
     * @param string $application The name of the application
     * @param string $path        The path of the application
     * @return void
     */
    public static function registerApplication($application, $path);

    /**
     * Get an application path
     *
     * @param string    $application   The name of the application
     * @return string	The path of the application
     */
    public static function getApplicationPath($application);

    /**
     * Get a list of applications
     *
     * @return array
     */
    public static function getApplications();

    /**
     * Set a package path
     *
     * @param string $package    The name of the package
     * @param string $path       The path of the package
     * @return void
     */
    public static function registerPackage($package, $path);

    /**
     * Get a package path
     *
     * @param string    $package   The name of the application
     * @return string	The path of the application
     */
    public static function getPackagePath($package);

    /**
     * Get a list of packages
     *
     * @return array
     */
    public static function getPackages();

    /**
     * Add a object locator
     *
     * @param KObjectLocatorInterface $locator
     * @return KObjectIdentifierInterface
     */
    public static function addLocator(KObjectLocatorInterface $locator);

    /**
     * Get the object locator
     *
     * @return KObjectLocatorInterface|null  Returns the object locator or NULL if the locator can not be found.
     */
    public function getLocator();

    /**
     * Get the decorators
     *
     *  @return array
     */
    public static function getLocators();

    /**
     * Check if the object is a singleton
     *
     * @return boolean Returns TRUE if the object is a multiton, FALSE otherwise.
     */
    public function isMultiton();

    /**
     * Check if the object is a singleton
     *
     * @return boolean Returns TRUE if the object is a singleton, FALSE otherwise.
     */
    public function isSingleton();

    /**
     * Formats the identifier as a [application::]type.component.[.path].name string
     *
     * @return string
     */
    public function toString();
}

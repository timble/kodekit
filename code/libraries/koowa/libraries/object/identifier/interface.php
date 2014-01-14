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
     * Get the identifier type
     *
     * @return string
     */
    public function getType();

    /**
     * Get the identifier domain
     *
     * @return string
     */
    public function getDomain();

    /**
     * Get the identifier package
     *
     * @return string
     */
    public function getPackage();

    /**
     * Get the identifier package
     *
     * @return array
     */
    public function getPath();

    /**
     * Get the identifier package
     *
     * @return string
     */
    public function getName();

    /**
     * Get the identifier class name
     *
     * @return string
     */
    public function getClass();

    /**
     * Set the identifier class name
     *
     * @param  string $class
     * @return KObjectIdentifierInterface
     */
    public function setClass($class);

    /**
     * Get the config
     *
     * @return KObjectConfig
     */
    public function getConfig();

    /**
     * Set the config
     *
     * @param  KObjectConfig|array $data   A ObjectConfig object or a an array of configuration options
     * @param   boolean           $merge  If TRUE the data in $config will be merged instead of replaced. Default TRUE.
     * @return  KObjectIdentifierInterface
     */
    public function setConfig($data, $merge = true);

    /**
     * Add a mixin
     *
     *  @param mixed $decorator An object implementing ObjectMixinInterface, an ObjectIdentifier or an identifier string
     * @param array $config     An array of configuration options
     * @return KObjectIdentifierInterface
     * @see Object::mixin()
     */
    public function addMixin($mixin, $config = array());

    /**
     * Get the mixins
     *
     *  @return array
     */
    public function getMixins();

    /**
     * Add a decorator
     *
     * @param mixed $decorator An object implementing ObjectDecoratorInterface, an ObjectIdentifier or an identifier string
     * @param array $config    An array of configuration options
     * @return KObjectIdentifierInterface
     * @see Object::decorate()
     */
    public function addDecorator($decorator, $config = array());

    /**
     * Get the decorators
     *
     *  @return array
     */
    public function getDecorators();

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

    /**
     * Formats the identifier as an associative array
     *
     * @return array
     */
    public function toArray();
}

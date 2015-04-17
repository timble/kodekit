<?php
/**
 * Nooku Framework - http://nooku.org/framework
 *
 * @copyright   Copyright (C) 2007 - 2014 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/nooku/nooku-framework for the canonical source repository
 */

/**
 * Object Identifier Interface
 *
 * Wraps identifiers of the form type:[//application/]package.[.path].name in an object, providing public accessors and
 * methods for derived formats.
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Koowa\Library\Object\Identifier
 */
interface KObjectIdentifierInterface extends Serializable
{
    /**
     * Constructor
     *
     * @param  string|array $identifier Identifier string or array in type://domain/package.[.path].name format
     * @param   array       $config     An optional associative array of configuration settings.
     * @throws  KObjectExceptionInvalidIdentifier If the identifier cannot be parsed
     */
    public function __construct($identifier, array $config = array());

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
     * Get the config
     *
     * @return KObjectConfig
     */
    public function getConfig();

    /**
     * Get the mixins
     *
     *  @return  KObjectConfig
     */
    public function getMixins();

    /**
     * Get the decorators
     *
     *  @return  KObjectConfig
     */
    public function getDecorators();

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

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
 * Object Identifier Interface
 *
 * Wraps identifiers of the form type:[//application/]package.[.path].name in an object, providing public accessors and
 * methods for derived formats.
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Kodekit\Library\Object\Identifier
 */
interface ObjectIdentifierInterface extends \Serializable
{
    /**
     * Constructor
     *
     * @param  string|array $identifier Identifier string or array in type://domain/package.[.path].name format
     * @param   array       $config     An optional associative array of configuration settings.
     * @throws  ObjectExceptionInvalidIdentifier If the identifier cannot be parsed
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
     * @return ObjectConfig
     */
    public function getConfig();

    /**
     * Get the mixins
     *
     *  @return  ObjectConfig
     */
    public function getMixins();

    /**
     * Get the decorators
     *
     *  @return  ObjectConfig
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

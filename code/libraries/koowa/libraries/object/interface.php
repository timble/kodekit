<?php
/**
 * Nooku Framework - http://nooku.org/framework
 *
 * @copyright   Copyright (C) 2007 - 2014 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/nooku/nooku-framework for the canonical source repository
 */

/**
 * Object Interface
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Koowa\Library\Object
 */
interface KObjectInterface
{
    /**
     * Constructor.
     *
     * @param   KObjectConfig $config Configuration options
     */
    //public function __construct(KObjectConfig $config);

    /**
     * Get an instance of a class based on a class identifier only creating it if it does not exist yet.
     *
     * @param   string|object   $identifier The class identifier or identifier object
     * @param   array           $config     An optional associative array of configuration settings.
     * @throws  RuntimeException if the object manager has not been defined.
     * @return  object Return object on success, throws exception on failure
     * @see     KObjectInterface
     */
    public function getObject($identifier, array $config = array());

    /**
     * Gets the object identifier.
     *
     * @param   null|KObjectIdentifier|string $identifier Identifier
     * @return	KObjectIdentifier
     *
     * @see     KObjectInterface
     * @throws  RuntimeException if the object manager has not been defined.
     */
    public function getIdentifier($identifier = null);

    /**
     * Get the object configuration
     *
     * If no identifier is passed the object config of this object will be returned. Function recursively
     * resolves identifier aliases and returns the aliased identifier.
     *
     *  @param string|object    $identifier A valid identifier string or object implementing ObjectInterface
     * @return KObjectConfig
     */
    public function getConfig($identifier = null);
}

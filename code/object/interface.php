<?php
/**
 * Kodekit - http://timble.net/kodekit
 *
 * @copyright   Copyright (C) 2007 - 2016 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/timble/kodekit for the canonical source repository
 */

namespace Kodekit\Library;

/**
 * Object Interface
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Kodekit\Library\Object
 */
interface ObjectInterface
{
    /**
     * Constructor.
     *
     * @param   ObjectConfig $config Configuration options
     */
    //public function __construct(ObjectConfig $config);

    /**
     * Get an instance of a class based on a class identifier only creating it if it does not exist yet.
     *
     * @param   string|object   $identifier The class identifier or identifier object
     * @param   array           $config     An optional associative array of configuration settings.
     * @throws  \RuntimeException if the object manager has not been defined.
     * @return  object Return object on success, throws exception on failure
     * @see     ObjectInterface
     */
    public function getObject($identifier, array $config = array());

    /**
     * Gets the object identifier.
     *
     * @param   null|ObjectIdentifier|string $identifier Identifier
     * @return	ObjectIdentifier
     *
     * @see     ObjectInterface
     * @throws  \RuntimeException if the object manager has not been defined.
     */
    public function getIdentifier($identifier = null);

    /**
     * Get the object configuration
     *
     * If no identifier is passed the object config of this object will be returned. Function recursively
     * resolves identifier aliases and returns the aliased identifier.
     *
     *  @param string|object    $identifier A valid identifier string or object implementing ObjectInterface
     * @return ObjectConfig
     */
    public function getConfig($identifier = null);
}

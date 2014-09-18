<?php
/**
 * Nooku Framework - http://nooku.org/framework
 *
 * @copyright   Copyright (C) 2007 - 2014 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/nooku/nooku-framework for the canonical source repository
 */

/**
 * Session Container Interface
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Koowa\Library\User\Session\Container
 */
interface KUserSessionContainerInterface
{
    /**
     * Get a an attribute
     *
     * @param   string $identifier  Attribute identifier, eg .foo.bar
     * @param   mixed  $default     Default value when the attribute doesn't exist
     * @return  mixed   The value
     */
    public function get($identifier, $default = null);

    /**
     * Set an attribute
     *
     * @param   mixed   $identifier Attribute identifier, eg foo.bar
     * @param   mixed   $value      Attribute value
     * @return KUserSessionContainerInterface
     */
    public function set($identifier, $value);

    /**
     * Check if an attribute exists
     *
     * @param   string  $identifier Attribute identifier, eg foo.bar
     * @return  boolean
     */
    public function has($identifier);

    /**
     * Removes an attribute
     *
     * @param string $identifier Attribute identifier, eg foo.bar
     * @return  KUserSessionContainerInterface
     */
    public function remove($identifier);

    /**
     * Clears out all attributes
     *
     * @return  KUserSessionContainerInterface
     */
    public function clear();

    /**
     * Adds new attributes
     *
     * @param array $attributes An array of attributes
     * @return  KUserSessionContainerInterface
     */
    public function add(array $attributes);

    /**
     * Load the attributes by reference
     *
     * After starting a session, PHP retrieves the session data through the session handler and populates $_SESSION
     * with the result automatically. This function can load the attributes from the $_SESSION global by reference
     * by passing the $_SESSION to this function.
     *
     * @param array $session The session data to load by reference.
     * @return KUserSessionContainerAbstract
     */
    public function load(array &$session);

    /**
     * Get all attributes
     *
     * @return  array  An array of attributes
     */
    public function toArray();

    /**
     * Set the session attributes namespace
     *
     * @param string $namespace The session attributes namespace
     * @return KUserSessionContainerAbstract
     */
    public function setNamespace($namespace);

    /**
     * Get the session attributes namespace
     *
     * @return string The session attributes namespace
     */
    public function getNamespace();

    /**
     * Get the session attributes separator
     *
     * @return string The session attribute separator
     */
    public function getSeparator();
}
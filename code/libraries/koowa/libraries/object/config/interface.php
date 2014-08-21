<?php
/**
 * Nooku Framework - http://nooku.org/framework
 *
 * @copyright   Copyright (C) 2007 - 2014 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/nooku/nooku-framework for the canonical source repository
 */

/**
 * Config Interface
 *
 * KObjectConfig provides a property based interface to an array. Data is can be modified unless the object is marked
 * as readonly.
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Koowa\Library\Object\Config
 */
interface KObjectConfigInterface extends IteratorAggregate, ArrayAccess, Countable
{
    /**
     * Retrieve a configuration option
     *
     * If the option does not exist return the default.
     *
     * @param string  $name
     * @param mixed   $default
     * @return mixed
     */
    public function get($name, $default = null);

    /**
     * Set a configuration option
     *
     * @param  string $name
     * @param  mixed  $value
     * @throws RuntimeException If the config is read only
     * @return KObjectConfig
     */
    public function set($name, $value);

    /**
     * Check if a configuration option exists
     *
     * @param  	string 	$name The configuration option name.
     * @return  boolean
     */
    public function has($name);

    /**
     * Remove a configuration option
     *
     * @param   string $name The configuration option name.
     * @throws  RuntimeException If the config is read only
     * @return  KObjectConfig
     */
    public function remove( $name );

    /**
     * Merge options
     *
     * This method will overwrite keys that already exist, keys that don't exist yet will be added.
     *
     * For duplicate keys, the following will be performed:
     *
     * - Nested configs will be recursively merged.
     * - Items in $options with INTEGER keys will be appended.
     * - Items in $options with STRING keys will overwrite current values.
     *
     * @param  array|Traversable|KObjectConfig  $options A KObjectConfig object an or array of options to be added
     * @throws RuntimeException If the config is read only
     * @return KObjectConfigInterface
     */
    public function merge($options);

    /**
     * Append options
     *
     * This function only adds keys that don't exist and it filters out any duplicate values
     *
     * @param  array|Traversable|KObjectConfig  $options A KObjectConfig object an or array of options to be appended
     * @throws RuntimeException If the config is read only
     * @return KObjectConfigInterface
     */
    public function append($options);

    /**
     * Return the data
     *
     * If the data being passed is an instance of KObjectConfig the data will be transformed
     * to an associative array.
     *
     * @param mixed|KObjectConfig $data
     * @return mixed|array
     */
    public static function unbox($data);

    /**
     * Return an associative array of the config data.
     *
     * @return array
     */
    public function toArray();

    /**
     * Prevent any more modifications being made to this instance.
     *
     * Useful after merge() has been used to merge multiple Config objects into one object which should then not be
     * modified again.
     *
     * @return KObjectConfigInterface
     */
    public function setReadOnly();

    /**
     * Returns whether this ObjectConfig object is read only or not.
     *
     * @return bool
     */
    public function isReadOnly();

    /**
     * Get a new instance
     *
     * @return KObjectConfigInterface
     */
    public function getInstance();
}

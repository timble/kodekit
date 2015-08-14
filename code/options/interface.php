<?php
/**
 * Nooku Framework - http://nooku.org/framework
 *
 * @copyright   Copyright (C) 2007 - 2015 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/nooku/nooku-framework for the canonical source repository
 */

/**
 * Options Interface
 *
 * @author  Israel Canasa <https://github.com/raeldc>
 * @package Koowa\Library\Options
 */
interface KOptionsInterface
{
    /**
     * Retrieve an option
     *
     * If the option does not exist return the default.
     *
     * @param string  $name
     * @param mixed   $default
     * @return mixed
     */
    public function get($name, $default = null);

    /**
     * Set an option
     *
     * @param  string $name
     * @param  mixed  $value
     * @throws RuntimeException If the config is read only
     * @return KOptionsInterface
     */
    public function set($name, $value);

    /**
     * Check if an option exists
     *
     * @param   string  $name The configuration option name.
     * @return  boolean
     */
    public function has($name);

    /**
     * Remove an option
     *
     * @param   string $name The configuration option name.
     * @throws  RuntimeException If the config is read only
     * @return  KOptionsInterface
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
     * @param  array|Traversable|KOptionsInterface  $options A KOptions object an or array of options to be added
     * @throws RuntimeException If the config is read only
     * @return KOptionsInterface
     */
    public function merge($options);

    /**
     * Append options
     *
     * This function only adds keys that don't exist and it filters out any duplicate values
     *
     * @param  array|Traversable|KOptionsInterface  $options A KObjectConfig object an or array of options to be appended
     * @throws RuntimeException If the config is read only
     * @return KOptionsInterface
     */
    public function append($options);

    /**
     * Return the data
     *
     * If the data being passed is an instance of KOptionsInterface the data will be transformed
     * to an associative array.
     *
     * @param mixed|KOptionsInterface $data
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
     * Useful after merge() has been used to merge multiple objects into one object which should then not be
     * modified again.
     *
     * @return KOptionsInterface
     */
    public function setReadOnly();

    /**
     * Returns whether this object is read only or not.
     *
     * @return bool
     */
    public function isReadOnly();

    /**
     * Get a new instance
     *
     * @return KOptionsInterface
     */
    public function getInstance();
}

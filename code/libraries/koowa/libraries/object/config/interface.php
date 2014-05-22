<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright	Copyright (C) 2007 - 2014 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/koowa for the canonical source repository
 */

/**
 * Config Interface
 *
 * KObjectConfig provides a property based interface to an array
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Koowa\Library\Config
 */
interface KObjectConfigInterface extends IteratorAggregate, ArrayAccess, Countable
{
    /**
     * Retrieve a configuration option
     *
     * If the option does not exist return the default
     *
     * @param string
     * @param mixed
     * @return mixed
     */
    public function get($name, $default = null);

    /**
     * Set a configuration option
     *
     * @param  string $name
     * @param  mixed  $value
     * @return void
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
     * @return  KObjectConfigInterface
     */
    public function remove( $name );

    /**
     * Add options
     *
     * This method will overwrite keys that already exist, keys that don't exist yet will be added.
     *
     * @param  array|KObjectConfig  $options A KObjectConfig object an or array of options to be added
     * @return KObjectConfigInterface
     */
    public function add($options);

    /**
     * Append values
     *
     * This function only adds keys that don't exist and it filters out any duplicate values
     *
     * @param  array|KObjectConfig  $options A KObjectConfig object an or array of options to be appended
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
}

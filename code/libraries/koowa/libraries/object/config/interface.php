<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright	Copyright (C) 2007 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
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
     * Retrieve a configuration item and return $default if there is no element set.
     *
     * @param string
     * @param mixed
     * @return mixed
     */
    public function get($name, $default = null);

    /**
     * Set a configuration item
     *
     * @param  string $name
     * @param  mixed  $value
     * @return void
     */
    public function set($name, $value);

    /**
     * Check if a configuration item exists
     *
     * @param  	string 	$name The configuration item name.
     * @return  boolean
     */
    public function has($name);

    /**
     * Remove a configuration item
     *
     * @param   string $name The configuration item name.
     * @return  KObjectConfigInterface
     */
    public function remove( $name );

    /**
     * Append values
     *
     * This function only adds keys that don't exist and it filters out any duplicate values
     *
     * @param  mixed    $config A value of an or array of values to be appended
     * @return KObjectConfig
     */
    public function append($config);

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
     * Return a ObjectConfig object from an array
     *
     * @param  array $array
     * @return KObjectConfig Returns a ObjectConfig object
     */
    public static function fromArray(array $array);
}

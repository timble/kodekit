<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright	Copyright (C) 2007 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/koowa for the canonical source repository
 */

/**
 * KObjectConfig provides a property based interface to an array
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Koowa\Library\Config
 */
class KObjectConfig implements KObjectConfigInterface
{
    /**
     * The data container
     *
     * @var array
     */
    protected $_data;

    /**
     * Constructor.
     *
     * @param   array|KObjectConfig An associative array of configuration settings or a KObjectConfig instance.
     */
    public function __construct( $config = array() )
    {
        if ($config instanceof KObjectConfig) {
            $data = $config->toArray();
        } else {
            $data = $config;
        }

        $this->_data = array();
        if (is_array($data))
        {
            foreach ($data as $key => $value) {
                $this->set($key, $value);
            }
        }
    }

    /**
     * Retrieve a configuration item and return $default if there is no element set.
     *
     * @param string
     * @param mixed
     * @return mixed
     */
    public function get($name, $default = null)
    {
        $result = $default;
        if(isset($this->_data[$name])) {
            $result = $this->_data[$name];
        }

        return $result;
    }

    /**
     * Set a configuration item
     *
     * @param  string $name
     * @param  mixed  $value
     * @return void
     */
    public function set($name, $value)
    {
        if (is_array($value)) {
            $this->_data[$name] = new self($value);
        } else {
            $this->_data[$name] = $value;
        }
    }

    /**
     * Check if a configuration item exists
     *
     * @param  	string 	$name The configuration item name.
     * @return  boolean
     */
    public function has($name)
    {
        return isset($this->_data[$name]);
    }

    /**
     * Remove a configuration item
     *
     * @param   string $name The configuration item name.
     * @return  KObjectConfig
     */
    public function remove( $name )
    {
        unset($this->_data[$name]);
        return $this;
    }

    /**
     * Append values
     *
     * This method only adds keys that don't exist and it filters out any duplicate values
     *
     * @param  mixed    $config A value of an or array of values to be appended
     * @return KObjectConfig
     */
    public function append($config)
    {
        $config = KObjectConfig::unbox($config);

        if(is_array($config))
        {
            if(!is_numeric(key($config)))
            {
                foreach($config as $key => $value)
                {
                    if(array_key_exists($key, $this->_data))
                    {
                        if(!empty($value) && ($this->_data[$key] instanceof KObjectConfig)) {
                            $this->_data[$key] = $this->_data[$key]->append($value);
                        }
                    }
                    else $this->__set($key, $value);
                }
            }
            else
            {
                foreach($config as $value)
                {
                    if (!in_array($value, $this->_data, true)) {
                        $this->_data[] = $value;
                    }
                }
            }
        }

        return $this;
    }

	/**
     * Return the data
     *
     * If the data being passed is an instance of KObjectConfig the data will be transformed to an associative array.
     *
     * @param mixed|KObjectConfig $data
     * @return mixed|array
     */
    public static function unbox($data)
    {
        return ($data instanceof KObjectConfig) ? $data->toArray() : $data;
    }

    /**
     * Get a new iterator
     *
     * @return  ArrayIterator
     */
    public function getIterator()
    {
        return new RecursiveArrayIterator($this->_data);
    }

    /**
     * Returns the number of elements in the collection.
     *
     * Required by the Countable interface
     *
     * @return int
     */
    public function count()
    {
        return count($this->_data);
    }

    /**
     * Check if the offset exists
     *
     * Required by interface ArrayAccess
     *
     * @param   int  $offset   The offset
     * @return  bool
     */
    public function offsetExists($offset)
    {
        return isset($this->_data[$offset]);
    }

    /**
     * Get an item from the array by offset
     *
     * Required by interface ArrayAccess
     *
     * @param   int  $offset   The offset
     * @return  mixed   The item from the array
     */
    public function offsetGet($offset)
    {
        $result = null;
        if(isset($this->_data[$offset]))
        {
            $result = $this->_data[$offset];
            if($result instanceof KObjectConfig) {
                $result = $result->toArray();
            }
        }

        return $result;
    }

    /**
     * Set an item in the array
     *
     * Required by interface ArrayAccess
     *
     * @param   int    $offset   The offset
     * @param   mixed  $value    The item's value
     *
     * @return  KObjectConfig
     */
    public function offsetSet($offset, $value)
    {
        $this->_data[$offset] = $value;
        return $this;
    }

    /**
     * Unset an item in the array
     *
     * All numerical array keys will be modified to start counting from zero while
     * literal keys won't be touched.
     *
     * Required by interface ArrayAccess
     *
     * @param   int     $offset The offset of the item
     * @return  KObjectConfig
     */
    public function offsetUnset($offset)
    {
        unset($this->_data[$offset]);

        return $this;
    }

    /**
     * Return an associative array of the config data.
     *
     * @return array
     */
    public function toArray()
    {
        $array = array();
        $data  = $this->_data;
        foreach ($data as $key => $value)
        {
            if ($value instanceof KObjectConfig) {
                $array[$key] = $value->toArray();
            } else {
                $array[$key] = $value;
            }
        }

        return $array;
    }

 	/**
     * Returns a string with the encapsulated data in JSON format
     *
     * @return string  Returns the data encoded to JSON
     */
    public function toJson()
    {
        return json_encode($this->toArray());
    }

    /**
     * Retrieve a configuration element
     *
     * @param string $name
     * @return mixed
     */
    public function __get($name)
    {
        return $this->get($name);
    }

    /**
     * Set a configuration element
     *
     * @param  string $name
     * @param  mixed  $value
     * @return void
     */
    public function __set($name, $value)
    {
        $this->set($name, $value);
    }

    /**
     * Test existence of a configuration element
     *
     * @param string $name
     * @return bool
     */
    public function __isset($name)
    {
        return $this->has($name);
    }

    /**
     * Unset a configuration element
     *
     * @param  string $name
     * @return void
     */
    public function __unset($name)
    {
        $this->remove($name);
    }

 	/**
     * Deep clone of this instance to ensure that nested KObjectConfigs
     * are also cloned.
     *
     * @return void
     */
    public function __clone()
    {
        $array = array();
        foreach ($this->_data as $key => $value)
        {
            if ($value instanceof KObjectConfig || $value instanceof stdClass) {
                $array[$key] = clone $value;
            } else {
                $array[$key] = $value;
            }
        }

        $this->_data = $array;
    }

    /**
     * Returns a string with the encapsulated data in JSON format
     *
     * @return string   returns the data encoded to JSON
     */
    public function __toString()
    {
        return $this->toJson();
    }
}

<?php
/**
 * Nooku Framework - http://nooku.org/framework
 *
 * @copyright   Copyright (C) 2007 - 2014 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/nooku/nooku-framework for the canonical source repository
 */

/**
 * KObjectConfig
 *
 * KObjectConfig provides a property based interface to an array. Data is can be modified unless the object is marked
 * as readonly.
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Koowa\Library\Object\Config
 */
class KObjectConfig implements KObjectConfigInterface
{
    /**
     * The configuration options
     *
     * @var array
     */
    private $__options = array();

    /**
     * Is the config data readonly
     *
     * @var bool
     */
    protected $_readonly;

    /**
     * Constructor.
     *
     * @param  array|KObjectConfig $options An associative array of configuration options or a ObjectConfig instance.
     * @param  bool $readonly  TRUE to not allow modifications of the config data. Default FALSE.
     */
    public function __construct( $options = array(), $readonly = false)
    {
        $this->_readonly = (bool) $readonly;

        $this->merge($options);
    }

    /**
     * Retrieve a configuration option
     *
     * If the option does not exist return the default.
     *
     * @param string  $name
     * @param mixed   $default
     * @return mixed
     */
    public function get($name, $default = null)
    {
        $result = $default;
        if(isset($this->__options[$name]) || array_key_exists($name, $this->__options)) {
            $result = $this->__options[$name];
        }

        return $result;
    }

    /**
     * Set a configuration option
     *
     * @param  string $name
     * @param  mixed  $value
     * @throws RuntimeException If the config is read only
     * @return KObjectConfig
     */
    public function set($name, $value)
    {
        if (!$this->isReadOnly())
        {
            if (is_array($value)) {
                $this->__options[$name] = $this->getInstance()->merge($value);
            } else {
                $this->__options[$name] = $value;
            }
        }
        else throw new RuntimeException('Config is read only');

        return $this;
    }

    /**
     * Check if a configuration option exists
     *
     * @param  	string 	$name The configuration option name.
     * @return  boolean
     */
    public function has($name)
    {
        return isset($this->__options[$name]);
    }

    /**
     * Remove a configuration option
     *
     * @param   string $name The configuration option name.
     * @throws  RuntimeException If the config is read only
     * @return  KObjectConfig
     */
    public function remove( $name )
    {
        if (!$this->isReadOnly()) {
            unset($this->__options[$name]);
        } else {
            throw new RuntimeException('Config is read only');
        }

        return $this;
    }

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
     * @param  array|Traversable|KObjectConfig $options A KObjectConfig object an or array of options to be appended
     * @throws RuntimeException If the config is read only
     * @return KObjectConfig
     */
    public function merge($options)
    {
        if (!$this->isReadOnly())
        {
            $options = self::unbox($options);

            if (is_array($options) || $options instanceof Traversable)
            {
                foreach ($options as $key => $value) {
                    $this->set($key, $value);
                }
            }
        }
        else throw new RuntimeException('Config is read only');

        return $this;
    }

    /**
     * Append values
     *
     * This method only adds keys that don't exist and it filters out any duplicate values
     *
     * @param  array|KObjectConfig|Traversable    $config A ObjectConfig object an or array of options to be appended
     * @throws RuntimeException If the config is read only
     * @return KObjectConfig
     */
    public function append($options)
    {
        if (!$this->isReadOnly())
        {
            $options = self::unbox($options);

            if(is_array($options) || $options instanceof Traversable)
            {
                if(!is_numeric(key($options)))
                {
                    foreach($options as $key => $value)
                    {
                        if(array_key_exists($key, $this->__options))
                        {
                            if(!empty($value) && ($this->__options[$key] instanceof KObjectConfig)) {
                                $this->__options[$key] = $this->__options[$key]->append($value);
                            }
                        }
                        else $this->set($key, $value);
                    }
                }
                else
                {
                    foreach($options as $value)
                    {
                        if (!in_array($value, $this->__options, true)) {
                            $this->__options[] = $value;
                        }
                    }
                }
            }
        }
        else throw new RuntimeException('Config is read only');

        return $this;
    }

    /**
     * Returns the number of elements in the collection.
     *
     * Required by the Countable interface
     *
     * @param int $mode Either COUNT_NORMAL or COUNT_RECURSIVE. Default is COUNT_NORMAL
     * @return int
     */
    public function count($mode = COUNT_NORMAL)
    {
        if($mode == COUNT_RECURSIVE)
        {
            $count = 0;
            $data  = $this->__options;
            foreach ($data as $key => $value)
            {
                if(is_array($value) || $value instanceof KObjectConfig)
                {
                    if ($value instanceof KObjectConfig) {
                        $count += $value->count($mode);
                    } else {
                        $count += count($value);
                    }
                }
            }
        }
        else $count = count($this->__options);

        return $count;
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
     * Get a new instance
     *
     * @return KObjectConfigInterface
     */
    public function getInstance()
    {
        $class    = get_class($this);
        $instance = new $class(array(), $this->_readonly);

        return $instance;
    }

    /**
     * Get a new iterator
     *
     * @return  ArrayIterator
     */
    public function getIterator()
    {
        return new RecursiveArrayIterator($this->__options);
    }

    /**
     * Check if the offset exists
     *
     * Required by interface ArrayAccess
     *
     * @param   int  $offset   The offset
     * @return  bool
     */
    final public function offsetExists($offset)
    {
        return $this->has($offset);
    }

    /**
     * Get an item from the array by offset
     *
     * Required by interface ArrayAccess
     *
     * @param   int  $offset   The offset
     * @return  mixed   The item from the array
     */
    final public function offsetGet($offset)
    {
        return self::unbox($this->get($offset));
    }

    /**
     * Set an item in the array
     *
     * Required by interface ArrayAccess
     *
     * @param   int    $offset   The offset
     * @param   mixed  $value    The item's value
     * @return  void
     */
    final public function offsetSet($offset, $value)
    {
        $this->set($offset, $value);
    }

    /**
     * Unset an item in the array
     *
     * All numerical array keys will be modified to start counting from zero while literal keys won't be touched.
     *
     * Required by interface ArrayAccess
     *
     * @param   int     $offset The offset of the item
     * @return  void
     */
    final public function offsetUnset($offset)
    {
        $this->remove($offset);
    }

    /**
     * Return an associative array of the config data.
     *
     * @return array
     */
    public function toArray()
    {
        $array = array();
        $data  = $this->__options;
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
     * Prevent any more modifications being made to this instance.
     *
     * Useful after merge() has been used to merge multiple Config objects into one object which should then not be
     * modified again.
     *
     * @return KObjectConfigInterface
     */
    public function setReadOnly()
    {
        $this->_readonly = true;

        foreach ($this->__options as $value)
        {
            if ($value instanceof KObjectConfig) {
                $value->setReadOnly();
            }
        }

        return $this;
    }

    /**
     * Returns whether this Config object is read only or not.
     *
     * @return bool
     */
    public function isReadOnly()
    {
        return $this->_readonly;
    }

    /**
     * Retrieve a configuration element
     *
     * @param string $name
     * @return mixed
     */
    final public function __get($name)
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
    final public function __set($name, $value)
    {
        $this->set($name, $value);
    }

    /**
     * Test existence of a configuration element
     *
     * @param string $name
     * @return bool
     */
    final public function __isset($name)
    {
        return $this->has($name);
    }

    /**
     * Unset a configuration element
     *
     * @param  string $name
     * @return void
     */
    final public function __unset($name)
    {
        $this->remove($name);
    }

 	/**
     * Deep clone of this instance to ensure that nested KObjectConfigs are also cloned.
     *
     * @return void
     */
    public function __clone()
    {
        $array = array();
        foreach ($this->__options as $key => $value)
        {
            if ($value instanceof KObjectConfig || $value instanceof stdClass) {
                $array[$key] = clone $value;
            } else {
                $array[$key] = $value;
            }
        }

        $this->__options = $array;
    }
}

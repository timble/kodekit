<?php
/**
 * Nooku Framework - http://nooku.org/framework
 *
 * @copyright   Copyright (C) 2007 - 2014 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/nooku/nooku-framework for the canonical source repository
 */

/**
 * Object Set
 *
 * A set is a data structure that can store objects, without any particular order, and no repeated values.  Unlike most
 * other collection types, rather than retrieving a specific element from a set, one typically tests if a object is
 * contained in the set.
 *
 * KObjectSet implements an associative container that stores objects, and in which the object themselves are the keys.
 * Objects are stored in the set in FIFO order.
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Koowa\Library\Object
 * @see     http://www.php.net/manual/en/class.splobjectstorage.php
 */
class KObjectSet extends KObject implements IteratorAggregate, ArrayAccess, Countable, Serializable
{
    /**
     * The objects
     *
     * @var array
     */
    protected $_data = array();

    /**
     * Constructor
     *
     * @param KObjectConfig $config  A ObjectConfig object with configuration options
     * @return KObjectSet
     */
    public function __construct(KObjectConfig $config)
    {
        parent::__construct($config);

        $this->_data = KObjectConfig::unbox($config->data);
    }

    /**
     * Initializes the options for the object
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param   KObjectConfig $config An optional ObjectConfig object with configuration options
     * @return  void
     */
    protected function _initialize(KObjectConfig $config)
    {
        $config->append(array(
            'data' => array(),
        ));

        parent::_initialize($config);
    }

    /**
     * Inserts an object in the set
     *
     * @param   KObjectHandlable $object
     * @return  boolean TRUE on success FALSE on failure
     */
    public function insert(KObjectHandlable $object)
    {
        $result = false;

        if ($handle = $object->getHandle())
        {
            $this->offsetSet($object, null);
            $result = true;
        }

        return $result;
    }

    /**
     * Removes an object from the set
     *
     * All numerical array keys will be modified to start counting from zero while literal keys won't be touched.
     *
     * @param   KObjectHandlable $object
     * @return  KObjectSet
     */
    public function remove(KObjectHandlable $object)
    {
        if ($this->offsetExists($object)) {
            $this->offsetUnset($object);
        }

        return $this;
    }

    /**
     * Checks if the set contains a specific object
     *
     * @param   KObjectHandlable $object
     * @return  bool Returns TRUE if the object is in the set, FALSE otherwise
     */
    public function contains(KObjectHandlable $object)
    {
        return $this->offsetExists($object);
    }

    /**
     * Merge-in another object set
     *
     * @param   KObjectSet  $set
     * @return  KObjectSet
     */
    public function merge(KObjectSet $set)
    {
        foreach ($set as $object) {
            $this->insert($object);
        }

        return $this;
    }

    /**
     * Check if the object exists in the queue
     *
     * Required by interface ArrayAccess
     *
     * @param   KObjectHandlable $object
     * @return  bool Returns TRUE if the object exists in the storage, and FALSE otherwise
     * @throws  \InvalidArgumentException if the object doesn't implement KObjectHandlable
     */
    public function offsetExists($object)
    {
        if (!$object instanceof KObjectHandlable) {
            throw new InvalidArgumentException('Object needs to implement KObjectHandlable');
        }

        return isset($this->_data[$object->getHandle()]);
    }

    /**
     * Returns the object from the set
     *
     * Required by interface ArrayAccess
     *
     * @param   KObjectHandlable $object
     * @return  KObjectHandlable
     * @throws  \InvalidArgumentException if the object doesn't implement KObjectHandlable
     */
    public function offsetGet($object)
    {
        if (!$object instanceof KObjectHandlable) {
            throw new InvalidArgumentException('Object needs to implement KObjectHandlable');
        }

        return $this->_data[$object->getHandle()];
    }

    /**
     * Store an object in the set
     *
     * Required by interface ArrayAccess
     *
     * @param   KObjectHandlable $object
     * @param   mixed            $value The data to associate with the object [UNUSED]
     * @throws InvalidArgumentException
     * @return  KObjectSet
     */
    public function offsetSet($object, $value)
    {
        if (!$object instanceof KObjectHandlable) {
            throw new InvalidArgumentException('Object needs to implement KObjectHandlable');
        }

        $this->_data[$object->getHandle()] = $object;
        return $this;
    }

    /**
     * Removes an object from the set
     *
     * Required by interface ArrayAccess
     *
     * @param   KObjectHandlable  $object
     * @return  KObjectSet
     * @throws  \InvalidArgumentException if the object doesn't implement the KObjectHandlable interface
     */
    public function offsetUnset($object)
    {
        if (!$object instanceof KObjectHandlable) {
            throw new InvalidArgumentException('Object needs to implement KObjectHandlable');
        }

        unset($this->_data[$object->getHandle()]);
        return $this;
    }

    /**
     * Return a string representation of the set
     *
     * Required by interface \Serializable
     *
     * @return  string  A serialized object
     */
    public function serialize()
    {
        return serialize($this->_data);
    }

    /**
     * Unserializes a set from its string representation
     *
     * Required by interface \Serializable
     *
     * @param   string  $serialized The serialized data
     */
    public function unserialize($serialized)
    {
        $this->_data = unserialize($serialized);
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
     * Return the first object in the set
     *
     * @return KObjectHandlable or NULL is queue is empty
     */
    public function top()
    {
        $objects = array_values($this->_data);

        $object = null;
        if (isset($objects[0])) {
            $object = $objects[0];
        }

        return $object;
    }

    /**
     * Defined by IteratorAggregate
     *
     * @return \ArrayIterator
     */
    public function getIterator()
    {
        return new ArrayIterator($this->_data);
    }

    /**
     * Return an associative array of the data.
     *
     * @return array
     */
    public function toArray()
    {
        return $this->_data;
    }
}

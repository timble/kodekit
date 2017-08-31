<?php
/**
 * Kodekit - http://timble.net/kodekit
 *
 * @copyright   Copyright (C) 2007 - 2016 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license     MPL v2.0 <https://www.mozilla.org/en-US/MPL/2.0>
 * @link        https://github.com/timble/kodekit for the canonical source repository
 */

namespace Kodekit\Library;

/**
 * Model Entity Collection
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Kodekit\Library\Model\Entity
 */
class ModelEntityComposite extends Object implements ModelEntityComposable
{
    /**
     * The entity set
     *
     * @var ObjectSet
     */
    private $__entities;

    /**
     * Name of the identity key in the collection
     *
     * @var    string
     */
    protected $_identity_key;

    /**
     * Clone entity object
     *
     * @var    boolean
     */
    protected $_prototypable;

    /**
     * The entity prototype
     *
     * @var  ModelEntityInterface
     */
    protected $_prototype;

    /**
     * Constructor
     *
     * @param ObjectConfig  $config  An optional ObjectConfig object with configuration options
     * @return ModelEntityComposite
     */
    public function __construct(ObjectConfig $config)
    {
        parent::__construct($config);

        $this->_prototypable = $config->prototypable;
        $this->_identity_key = $config->identity_key;

        // Reset the collection
        $this->clear();

        // Insert the data, if exists
        if (!empty($config->data))
        {
            foreach($config->data->toArray() as $properties) {
                $this->insert($properties,$config->status);
            }
        }
    }

    /**
     * Initializes the options for the object
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param   ObjectConfig $config An optional ObjectConfig object with configuration options
     * @return  void
     */
    protected function _initialize(ObjectConfig $config)
    {
        $config->append(array(
            'data'         => null,
            'identity_key' => null,
            'prototypable' => true
        ));

        parent::_initialize($config);
    }

    /**
     * Insert a new entity
     *
     * This function will either clone a entity prototype or create a new instance of the entity object for each
     * entity being inserted. By default the entity will be cloned. The entity will be stored by it's identity_key
     * if set or otherwise by it's object handle.
     *
     * @param   ModelEntityInterface|array $entity  A ModelEntityInterface object or an array of entity properties
     * @param   string  $status     The entity status
     * @return  ModelEntityComposite
     */
    public function insert($entity, $status = null)
    {
        if(!$entity instanceof ModelEntityInterface)
        {
            if (!is_array($entity) && !$entity instanceof \Traversable)
            {
                throw new \InvalidArgumentException(sprintf(
                    'Entity must be an array or an object implementing the Traversable interface; received "%s"', gettype($entity)
                ));
            }

            if($this->_prototypable)
            {
                if(!$this->_prototype instanceof ModelEntityInterface)
                {
                    $identifier = $this->getIdentifier()->toArray();
                    $identifier['path'] = array('model', 'entity');
                    $identifier['name'] = StringInflector::singularize($this->getIdentifier()->name);

                    //The entity default options
                    $options = array(
                        'identity_key' => $this->getIdentityKey()
                    );

                    $this->_prototype = $this->getObject($identifier, $options);
                }

                $prototype = clone $this->_prototype;
                $prototype->setStatus($status);
                $prototype->setProperties($entity, $prototype->isNew());

                $entity = $prototype;
            }
            else
            {
                $identifier = $this->getIdentifier()->toArray();
                $identifier['path'] = array('model', 'entity');
                $identifier['name'] = StringInflector::singularize($this->getIdentifier()->name);

                //The entity default options
                $options = array(
                    'data'         => $entity,
                    'status'       => $status,
                    'identity_key' => $this->getIdentityKey()
                );

                $entity = $this->getObject($identifier, $options);
            }
        }

        //Insert the entity into the collection
        $this->__entities->insert($entity);

        return $this;
    }

    /**
     * Find an entity in the collection based on a needle
     *
     * This functions accepts either a know position or associative array of property/value pairs
     *
     * @param   string|array  $needle The position or the key or an associative array of column data to match
     * @return  ModelEntityComposite Returns a collection if successful or FALSE
     */
    public function find($needle)
    {
        //Filter the objects
        $objects = $this->__entities->filter(function($object) use ($needle)
        {
            if(is_array($needle))
            {
                foreach($needle as $key => $value)
                {
                    if(!in_array($object->getProperty($key), (array) $value)) {
                        return false;
                    }
                }
            }
            else return (bool) ($object->getHandle() == $needle);
        });

        $result = false;
        if(is_array($needle) || count($objects))
        {
            //Create the entities
            $result = clone $this;
            $result->clear();

            //Create the resultset
            foreach($objects as $object) {
                $result->insert($object);
            }
        }

        return $result;
    }

    /**
     * Removes an entity from the collection
     *
     * The entity will be removed based on it's identity_key if set or otherwise by it's object handle.
     *
     * @param  ModelEntityInterface $entity
     * @throws \InvalidArgumentException if the object doesn't implement ModelEntityInterface
     * @return ModelEntityComposite
     */
    public function remove($entity)
    {
        if (!$entity instanceof ModelEntityInterface) {
            throw new \InvalidArgumentException('Entity needs to implement ModelEntityInterface');
        }

        return $this->__entities->remove($entity);
    }

    /**
     * Checks if the collection contains a specific entity
     *
     * @param   ModelEntityInterface $entity
     * @throws \InvalidArgumentException if the object doesn't implement ModelEntityInterface
     * @return  bool Returns TRUE if the object is in the set, FALSE otherwise
     */
    public function contains($entity)
    {
        if (!$entity instanceof ModelEntityInterface) {
            throw new \InvalidArgumentException('Entity needs to implement ModelEntityInterface');
        }

        return $this->__entities->contains($entity);
    }

    /**
     * Store all entities in the collection to the data store
     *
     * @return boolean  If successful return TRUE, otherwise FALSE
     */
    public function save()
    {
        $result = false;

        if ($this->count())
        {
            $result = true;

            foreach ($this as $entity)
            {
                if (!$entity->save())
                {
                    // Set current entity status message as collection status message.
                    $this->setStatusMessage($entity->getStatusMessage());
                    $result = false;
                }
            }
        }

        return $result;
    }

    /**
     * Remove all entities in the collection from the data store
     *
     * @return bool  If successful return TRUE, otherwise FALSE
     */
    public function delete()
    {
        $result = false;

        if ($this->count())
        {
            $result = true;

            foreach ($this as $entity)
            {
                if (!$entity->delete())
                {
                    // Set current entity status message as collection status message.
                    $this->setStatusMessage($entity->getStatusMessage());
                    $result = false;
                }
            }
        }

        return $result;
    }

    /**
     * Clear the collection
     *
     * @return  ModelEntityComposite
     */
    public function clear()
    {
        $this->__entities = $this->getObject('object.set');
        return $this;
    }

    /**
     * Gets the identity key
     *
     * @return string
     */
    public function getIdentityKey()
    {
        return $this->_identity_key;
    }

    /**
     * Get a property
     *
     * @param   string  $name The property name.
     * @return  mixed
     */
    public function getProperty($name)
    {
        $result = null;
        if($entity = $this->getIterator()->current()) {
            $result = $entity->getProperty($name);
        }

        return $result;
    }

    /**
     * Set a property
     *
     * @param   string  $name       The property name.
     * @param   mixed   $value      The property value.
     * @param   boolean $modified   If TRUE, update the modified information for the property
     * @return  ModelEntityComposite
     */
    public function setProperty($name, $value, $modified = true)
    {
        if($entity = $this->getIterator()->current()) {
            $entity->setProperty($name, $value, $modified);
        }

        return $this;
    }

    /**
     * Test existence of a property
     *
     * @param  string  $name The property name.
     * @return boolean
     */
    public function hasProperty($name)
    {
        $result = false;
        if($entity = $this->getIterator()->current()) {
            $result = $entity->hasProperty($name);
        }

        return $result;
    }

    /**
     * Remove a property
     *
     * @param   string  $name The property name.
     * @return  ModelEntityComposite
     */
    public function removeProperty($name)
    {
        if($entity = $this->getIterator()->current()) {
            $entity->removeProperty($name);
        }

        return $this;
    }

    /**
     * Get the properties
     *
     * @param   boolean  $modified If TRUE, only return the modified data.
     * @return  array   An associative array of the entity properties
     */
    public function getProperties($modified = false)
    {
        $result = array();

        if($entity = $this->getIterator()->current()) {
            $result = $entity->getProperties($modified);
        }

        return $result;
    }

    /**
     * Set the properties
     *
     * @param   mixed   $properties Either and associative array, an object or a ModelEntityInterface
     * @param   boolean $modified   If TRUE, update the modified information for each column being set.
     * @return  ModelEntityComposite
     */
    public function setProperties($properties, $modified = true)
    {
        //Prevent changing the identity key
        if (isset($this->_identity_key)) {
            unset($properties[$this->_identity_key]);
        }

        if($entity = $this->getIterator()->current()) {
            $entity->setProperties($properties, $modified);
        }

        return $this;
    }

    /**
     * Get a list of the computed properties
     *
     * @return array An array
     */
    public function getComputedProperties()
    {
        $result = array();

        if($entity = $this->getIterator()->current()) {
            $result = $entity->getComputedProperties();
        }

        return $result;
    }

    /**
     * Returns the status
     *
     * @return string The status
     */
    public function getStatus()
    {
        $status = null;

        if($entity = $this->getIterator()->current()) {
            $status = $entity->getStatus();
        }

        return $status;
    }

    /**
     * Set the status
     *
     * @param   string|null  $status The status value or NULL to reset the status
     * @return  ModelEntityComposite
     */
    public function setStatus($status)
    {
        if($entity = $this->getIterator()->current()) {
            $entity->setStatusMessage($status);
        }

        return $this;
    }

    /**
     * Returns the status message
     *
     * @return string The status message
     */
    public function getStatusMessage()
    {
        $message = false;

        if($entity = $this->getIterator()->current()) {
            $message = $entity->getStatusMessage($message);
        }

        return $message;
    }

    /**
     * Set the status message
     *
     * @param   string $message The status message
     * @return  ModelEntityComposite
     */
    public function setStatusMessage($message)
    {
        if($entity = $this->getIterator()->current()) {
            $entity->setStatusMessage($message);
        }

        return $this;
    }

    /**
     * Checks if the current entity is new or not
     *
     * @return boolean
     */
    public function isNew()
    {
        $result = true;
        if($entity = $this->getIterator()->current()) {
            $result = $entity->isNew();
        }

        return $result;
    }

    /**
     * Check if a the current entity or specific entity property has been modified.
     *
     * If a specific property name is giving method will return TRUE only if this property was modified.
     *
     * @param   string $property The property name
     * @return  boolean
     */
    public function isModified($property = null)
    {
        $result = false;
        if($entity = $this->getIterator()->current()) {
            $result = $entity->isModified($property);
        }

        return $result;
    }

    /**
     * Test if the entity is connected to a data store
     *
     * @return	bool
     */
    public function isConnected()
    {
        $result = false;
        if($entity = $this->getIterator()->current()) {
            $result = $entity->isConnected();
        }

        return $result;
    }

    /**
     * Return an associative array of the data.
     *
     * @return array
     */
    public function toArray()
    {
        $result = array();
        if($entity = $this->getIterator()->current()) {
            $result = $entity->toArray();
        }

        return $result;
    }

    /**
     * Get a handle for this object
     *
     * This function returns an unique identifier for the object. This id can be used as a hash key for storing objects
     * or for identifying an object
     *
     * @return string A string that is unique
     */
    public function getHandle()
    {
        $result = false;
        if($entity = $this->getIterator()->current()) {
            $result = $entity->getHandle();
        }

        return $result;
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
        return $this->__entities->serialize();
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
        $this->__entities->unserialize($serialized);
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
        return $this->__entities->count();
    }

    /**
     * Defined by IteratorAggregate
     *
     * @return \ArrayIterator
     */
    public function getIterator()
    {
        return $this->__entities->getIterator();
    }

    /**
     * Set a property
     *
     * @param   string  $property   The property name.
     * @param   mixed   $value      The property value.
     * @return  void
     */
    final public function offsetSet($property, $value)
    {
        $this->setProperty($property, $value);
    }

    /**
     * Get a property
     *
     * @param   string  $property   The property name.
     * @return  mixed The property value
     */
    final public function offsetGet($property)
    {
        return $this->getProperty($property);
    }

    /**
     * Check if a property exists
     *
     * @param   string  $property   The property name.
     * @return  boolean
     */
    final public function offsetExists($property)
    {
        return $this->hasProperty($property);
    }

    /**
     * Remove a property
     *
     * @param   string  $property The property name.
     * @return  void
     */
    final public function offsetUnset($property)
    {
        $this->removeProperty($property);
    }

    /**
     * Set a property
     *
     * @param   string  $property   The property name.
     * @param   mixed   $value      The property value.
     * @return  void
     */
    final public function __set($property, $value)
    {
        $this->setProperty($property, $value);
    }

    /**
     * Test existence of a property
     *
     * @param  string  $property The property name.
     * @return boolean
     */
    final public function __isset($property)
    {
        return $this->hasProperty($property);
    }

    /**
     * Remove a property
     *
     * @param   string  $property The property name.
     * @return  ModelEntityComposite
     */
    final public function __unset($property)
    {
        $this->removeProperty($property);
    }

    /**
     * Forward the call to the current entity
     *
     * @param  string   $method    The function name
     * @param  array    $arguments The function arguments
     * @throws \BadMethodCallException   If method could not be found
     * @return mixed The result of the function
     */
    public function __call($method, $arguments)
    {
        $result = null;

        if($entity = $this->getIterator()->current())
        {
            // Call_user_func_array is ~3 times slower than direct method calls.
            switch (count($arguments))
            {
                case 0 :
                    $result = $entity->$method();
                    break;
                case 1 :
                    $result = $entity->$method($arguments[0]);
                    break;
                case 2 :
                    $result = $entity->$method($arguments[0], $arguments[1]);
                    break;
                case 3 :
                    $result = $entity->$method($arguments[0], $arguments[1], $arguments[2]);
                    break;
                default:
                    // Resort to using call_user_func_array for many segments
                    $result = call_user_func_array(array($entity, $method), $arguments);
            }
        }

        return $result;
    }
}
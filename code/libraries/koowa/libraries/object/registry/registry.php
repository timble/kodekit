<?php
/**
 * Nooku Framework - http://nooku.org/framework
 *
 * @copyright   Copyright (C) 2007 - 2014 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/nooku/nooku-framework for the canonical source repository
 */

/**
 * Object Registry
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Koowa\Library\Object\Registry
 */
class KObjectRegistry extends ArrayObject implements KObjectRegistryInterface
{
    /**
     * The identifier classes
     *
     * @var  array
     */
    protected $_classes = array();

    /**
     * The identifier aliases
     *
     * @var  array
     */
    protected $_aliases = array();

    /**
     * Get a an object from the registry
     *
     * @param  KObjectIdentifier|string $identifier An ObjectIdentifier, identifier string
     * @return KObjectInterface   The object or NULL if the identifier could not be found
     */
    public function get($identifier)
    {
        $identifier = (string) $identifier;

        if($this->offsetExists($identifier)) {
            $result = $this->offsetGet($identifier);
        } else {
            $result = null;
        }

        return $result;
    }

    /**
     * Set an object in the registry
     *
     * @param  KObjectIdentifier|string $identifier An ObjectIdentifier, identifier string
     * @param  mixed $data
     * @return KObjectIdentifier The object identifier that was set in the registry.
     */
    public function set($identifier, $data = null)
    {
        if($data == null) {
            $data = is_string($identifier) ? new KObjectIdentifier($identifier) : $identifier;
        }

        $this->offsetSet((string) $identifier, $data);
        return $data;
    }

    /**
     * Check if an object exists in the registry
     *
     * @param  KObjectIdentifier|string $identifier An ObjectIdentifier, identifier string
     * @return boolean
     */
    public function has($identifier)
    {
        return $this->offsetExists((string) $identifier);
    }

    /**
     * Remove an object from the registry
     *
     * @param  KObjectIdentifier|string $identifier An ObjectIdentifier, identifier string
     * @return KObjectRegistry
     */
    public function remove($identifier)
    {
        $this->offsetUnset((string) $identifier);
        return $this;
    }

    /**
     * Clears out all objects from the registry
     *
     * @return KObjectRegistry
     */
    public function clear()
    {
        $this->exchangeArray(array());
        return $this;
    }

    /**
     * Try to find an object based on an identifier string
     *
     * @param   mixed  $identifier
     * @return  KObjectIdentifier  An ObjectIdentifier or NULL if the identifier does not exist.
     */
    public function find($identifier)
    {
        $identifier = (string) $identifier;

        //Resolve the real identifier in case an alias was passed
        while(array_key_exists($identifier, $this->_aliases)) {
            $identifier = $this->_aliases[$identifier];
        }

        //Find the identifier
        if($this->offsetExists($identifier))
        {
            $result = $this->offsetGet($identifier);

            if($result instanceof KObjectInterface) {
                $result = $result->getIdentifier();
            }
        }
        else  $result = null;

        return $result;
    }

    /**
     * Register an alias for an identifier
     *
     * @param  KObjectIdentifier|string $identifier An ObjectIdentifier, identifier string
     * @param mixed  $alias  The alias
     * @return KObjectRegistry
     */
    public function alias($identifier, $alias)
    {
        $alias      = trim((string) $alias);
        $identifier = (string) $identifier;

        //Don't register the alias if it's the same as the identifier
        if($alias != $identifier) {
            $this->_aliases[$alias] = (string) $identifier;
        }

        return $this;
    }

    /**
     * Register a class for an identifier
     *
     * @param  KObjectIdentifier|string $identifier An ObjectIdentifier, identifier string
     * @param string $class The class
     * @return KObjectRegistry
     */
    public function setClass($identifier, $class)
    {
        $class      = trim($class);
        $identifier = (string) $identifier;

        $this->_classes[$identifier] = $class;

        return $this;
    }

    /**
     * Get the identifier class
     *
     * @param  KObjectIdentifier|string $identifier An ObjectIdentifier, identifier string
     * @return string|false|null  Returns the class name or FALSE if the class could not be found.
     */
    public function getClass($identifier)
    {
        $result     = null;
        $identifier = (string) $identifier;

        if(isset($this->_classes[$identifier])) {
            $result = $this->_classes[$identifier];
        }

        return $result;
    }

    /**
     * Get a list of all the identifier aliases
     *
     * @return array
     */
    public function getClasses()
    {
        return $this->_classes;
    }

    /**
     * Get a list of all the identifier aliases
     *
     * @return array
     */
    public function getAliases()
    {
        return $this->_aliases;
    }

    /**
     * Get a list of all identifiers in the registry
     *
     * @return  array
     */
    public function getIdentifiers()
    {
        return array_keys($this->getArrayCopy());
    }
}
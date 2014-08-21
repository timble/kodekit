<?php
/**
 * Nooku Framework - http://nooku.org/framework
 *
 * @copyright   Copyright (C) 2007 - 2014 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/nooku/nooku-framework for the canonical source repository
 */

/**
 * Message User Session Container
 *
 * Session container that stores flash messages and provides utility functions. Flash messages are self-expiring
 * messages that are meant to live for exactly one request (they're "gone in a flash"). They're designed to be used
 * across redirects.
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Koowa\Library\User\Session\Container
 */
class KUserSessionContainerMessage extends KUserSessionContainerAbstract
{
    /**
     * The previous flash messages
     *
     * @var array
     */
    protected $_previous = array();

    /**
     * Get all the previous flash messages and flush them from the container
     *
     * @return array
     */
    public function all()
    {
        $result = $this->_previous;
        $this->_previous = array();

        return $result;
    }

    /**
     * Get previous flash messages for a given type and flush them from the container
     *
     * @param string $type    Message category type.
     * @param array  $default Default value if $type does not exist.
     * @return array
     */
    public function get($type, $default = array())
    {
        if(isset($this->_previous[$type]))
        {
            $result = $this->_previous[$type];
            unset($this->_previous[$type]);
        }
        else $result = $default;

        return $result;
    }

    /**
     * Set current flash messages for a given type.
     *
     * @param string       $type    Message category type.
     * @param string|array $messages
     * @return KUserSessionContainerMessage
     */
    public function set($type, $messages)
    {
        foreach((array) $messages as $message) {
            parent::set($type, $message);
        }

        return $this;
    }

    /**
     * Has current flash messages for a given type?
     *
     * @param string $type  Message category type.
     * @return boolean
     */
    public function has($type)
    {
        return parent::has($type);
    }

    /**
     * Removes current flash messages for a given type
     *
     * @param string $type  Message category type.
     * @return KUserSessionContainerMessage
     */
    public function remove($type)
    {
        parent::remove($type);
        return $this;
    }

    /**
     * Clears out all current flash messages
     *
     * @return KUserSessionContainerMessage
     */
    public function clear()
    {
        parent::clear();
        return $this;
    }

    /**
     * Add new flash messages
     *
     * @param array $messages An of messages per type
     * @return KUserSessionContainerMessage
     */
    public function add(array $messages)
    {
        parent::add($messages);
        return $this;
    }

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
    public function load(array &$session)
    {
        parent::load($session);

        $this->_previous = $this->toArray();
        $this->clear();

        return $this;
    }

    /**
     * Returns a list of all defined types.
     *
     * @return array
     */
    public function types()
    {
        return array_keys($this->_data);
    }
    /**
     * Get an item from the array by offset
     *
     * Required by interface ArrayAccess
     *
     * @param   int     $offset
     * @return  mixed The item from the array
     */
    public function offsetGet($offset)
    {
        return KObjectArray::offsetGet($offset);
    }

    /**
     * Set an item in the array
     *
     * Required by interface ArrayAccess
     *
     * @param   int     $offset
     * @param   mixed   $value
     * @return  void
     */
    public function offsetSet($offset, $value)
    {
        if(!isset($this->_data[$offset])) {
            $this->_data[$offset] = array();
        }

        $this->_data[$offset][] = $value;
    }

    /**
     * Check if the offset exists
     *
     * Required by interface ArrayAccess
     *
     * @param   int   $offset
     * @return  bool
     */
    public function offsetExists($offset)
    {
        return KObjectArray::offsetExists($offset);
    }

    /**
     * Unset an item in the array
     *
     * Required by interface ArrayAccess
     *
     * @param   int     $offset
     * @return  void
     */
    public function offsetUnset($offset)
    {
        KObjectArray::offsetUnset($offset);
    }
}
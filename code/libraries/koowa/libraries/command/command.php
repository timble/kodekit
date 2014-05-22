<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright	Copyright (C) 2007 - 2014 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/koowa for the canonical source repository
 */

/**
 * Command Context
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Koowa\Library\Command
 */
class KCommand extends KObjectConfig implements KCommandInterface
{
    /**
     * The event name
     *
     * @var array
     */
    protected $_name;

    /**
     * Subject of the command
     *
     * @var mixed
     */
    protected $_subject;

    /**
     * Constructor.
     *
     * @param  string              $name       The command name
     * @param  array|Traversable   $attributes An associative array or a Traversable object instance
     * @param  mixed               $subject    The command subject
     */
    public function __construct($name = '', $attributes = array(), $subject = null)
    {
        parent::__construct($attributes);

        $this->setName($name);
        $this->setSubject($subject);
    }

    /**
     * Get the event name
     *
     * @return string	The event name
     */
    public function getName()
    {
        return $this->_name;
    }

    /**
     * Set the event name
     *
     * @param string $name  The event name
     * @return KCommand
     */
    public function setName($name)
    {
        $this->_name = $name;
        return $this;
    }

    /**
     * Get the command subject
     *
     * @return mixed The command subject
     */
    public function getSubject()
    {
        return $this->_subject;
    }

    /**
     * Set the command subject
     *
     * @param mixed $subject The command subject
     * @return KCommand
     */
    public function setSubject($subject)
    {
        $this->_subject = $subject;
        return $this;
    }

    /**
     * Set attributes
     *
     * Overwrites existing attributes
     *
     * @param  array|Traversable $attributes
     * @throws InvalidArgumentException If the attributes are not an array or are not traversable.
     * @return KCommand
     */
    public function setAttributes($attributes)
    {
        if (!is_array($attributes) || $attributes instanceof Traversable)
        {
            throw new InvalidArgumentException(sprintf(
                'Event arguments must be an array or an object implementing the Traversable interface; received "%s"', gettype($attributes)
            ));
        }

        //Set the arguments.
        foreach ($attributes as $key => $value) {
            $this->set($key, $value);
        }

        return $this;
    }

    /**
     * Get all arguments
     *
     * @return array
     */
    public function getAttributes()
    {
        return $this->toArray();
    }

    /**
     * Get an attribute
     *
     * If the attribute does not exist, the $default value will be returned.
     *
     * @param  string $name The attribute name
     * @param  mixed $default
     * @return mixed
     */
    public function getAttribute($name, $default = null)
    {
        return $this->get($name, $default);
    }

    /**
     * Set an attribute
     *
     * @param  string $name The attribute
     * @param  mixed $value
     * @return KCommand
     */
    public function setAttribute($name, $value)
    {
        $this->set($name, $value);
        return $this;
    }

    /**
     * Set a command property
     *
     * @param  string $name
     * @param  mixed  $value
     * @return void
     */
    public function set($name, $value)
    {
        if (is_array($value)) {
            $value = new KObjectConfig($value);
        }

        parent::set($name, $value);
    }

    /**
     * Get a command property or attribute
     *
     * If a command property exists the property will be returned, otherwise the attribute will be returned. If no
     * property or attribute can be found the method will return NULL.
     *
     * @param  string $name
     * @return mixed|null  The property value
     */
    public function __get($name)
    {
        $getter = 'get'.ucfirst($name);
        if(method_exists($this, $getter)) {
            $value = $this->$getter();
        } else {
            $value = parent::__get($name);
        }

        return $value;
    }

    /**
     * Set a command property or attribute
     *
     * If a command property exists the property will be set, otherwise an attribute will be added.
     *
     * @param  string $name
     * @param  mixed  $value
     * @return void
     */
    public function __set($name, $value)
    {
        $setter = 'set'.ucfirst($name);
        if(method_exists($this, $setter)) {
            $this->$setter($value);
        } else {
            parent::__set($name, $value);
        }
    }
}

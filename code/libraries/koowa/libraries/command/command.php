<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright	Copyright (C) 2007 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
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
     * Get the command subject
     *
     * @return object	The command subject
     */
    public function getSubject()
    {
        return $this->get('subject');
    }

    /**
     * Set the command subject
     *
     * @param KObjectInterface $subject The command subject
     * @return $this
     */
    public function setSubject(KObjectInterface $subject)
    {
        $this->set('subject', $subject);
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
            $this->_data[$name] = new KObjectConfig($value);
        } else {
            $this->_data[$name] = $value;
        }
    }

    /**
     * Get a command property
     *
     * @param  string $name
     * @return mixed  The property value
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
     * Set a command property
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

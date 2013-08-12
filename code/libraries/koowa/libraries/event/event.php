<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright	Copyright (C) 2007 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/koowa for the canonical source repository
 */

/**
 * Event
 *
 * You can call the method stopPropagation() to abort the execution of further listeners in your event listener.
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Koowa\Library\Event
 */
class KEvent extends KConfig implements KEventInterface
{
 	/**
     * Priority levels
     */
    const PRIORITY_HIGHEST = 1;
    const PRIORITY_HIGH    = 2;
    const PRIORITY_NORMAL  = 3;
    const PRIORITY_LOW     = 4;
    const PRIORITY_LOWEST  = 5;

 	/**
     * The propagation state of the event
     *
     * @var boolean
     */
    protected $_propagate = true;

 	/**
     * The event name
     *
     * @var array
     */
    protected $_name;

    /**
     * Constructor.
     *
     * @param	string 			The event name
     * @param   array|KConfig 	An associative array of configuration settings or a KConfig instance.
     */
    public function __construct( $name, $config = array() )
    {
    	$this->_data = array();
        if (is_array($config) || $config instanceof Traversable)
        {
            foreach ($config as $key => $value) {
                $this->__set($key, $value);
            }
        }
        
        //Set the command name
        $this->_name = $name;
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
     * Returns whether further event listeners should be triggered.
     *
     * @return boolean 	TRUE if the event can propagate. Otherwise FALSE
     */
    public function canPropagate()
    {
        return $this->_propagate;
    }

    /**
     * Stops the propagation of the event to further event listeners.
     *
     * If multiple event listeners are connected to the same event, no
     * further event listener will be triggered once any trigger calls
     * stopPropagation().
     *
     * @return KEvent
     */
    public function stopPropagation()
    {
        $this->_propagate = false;
        return $this;
    }
}

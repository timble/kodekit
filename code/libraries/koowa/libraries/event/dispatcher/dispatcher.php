<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright	Copyright (C) 2007 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/koowa for the canonical source repository
 */

/**
 * Event Dispatcher
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Koowa\Library\Event
 */
class KEventDispatcher extends KObject implements KEventDispatcherInterface
{
    /**
	 * An associative array of event listeners queues
	 *
	 * The keys are holding the event name and the value in an KObjectQueue object.
	 *
	 * @var array
	 */
	protected $_listeners;

	/**
     * The event object
     *
     * @var KEvent
     */
    protected $_event = null;

	/**
     * Constructor.
     *
     * @param   KObjectConfig $config Configuration options
     */
	public function __construct(KObjectConfig $config = null)
	{
		parent::__construct($config);

	    $this->_listeners = array();
	}

    /**
     * Dispatches an event by dispatching arguments to all listeners that handle the event and returning
     * their return values.
     *
     * @param   string  $name  The event name
     * @param   KEvent|array   An array, a KObjectConfig or a KEvent object
     * @return  KEventDispatcher
     */
    public function dispatchEvent($name, $event = array())
    {
        //Make sure we have an event object
        if(!$event instanceof KEvent) {
            $event = new KEvent($name, $event);
        }

        //Notify the listeners
        if(isset($this->_listeners[$name]))
        {
            foreach($this->_listeners[$name] as $listener)
            {
                $listener->$name($event);

                if (!$event->canPropagate()) {
                    break;
                }
            }
        }

        return $this;
    }

    /**
     * Add an event listener
     *
     * @param  string            $name The event name
     * @param  KObjectHandlable  $listener An object implementing the KObjectHandlable interface
     * @param  integer           $priority The event priority, usually between 1 (high priority) and 5 (lowest),
     *                                     default is 3. If no priority is set, the command priority will be used
     *                                     instead.
     * @return KEventDispatcher
     */
    public function addEventListener($name, KObjectHandlable $listener, $priority = KEvent::PRIORITY_NORMAL)
    {
        if(is_object($listener))
        {
            if(!isset($this->_listeners[$name])) {
                $this->_listeners[$name] = new KObjectQueue();
            }

            $this->_listeners[$name]->enqueue($listener, $priority);
        }

        return $this;
    }

    /**
     * Remove an event listener
     *
     * @param   string           $name      The event name
     * @param   KObjectHandlable $listener  An object implementing the KObjectHandlable interface
     * @return  KEventDispatcher
     */
    public function removeEventListener($name, KObjectHandlable $listener)
    {
        if(is_object($listener))
        {
            if(isset($this->_listeners[$name])) {
                $this->_listeners[$name]->dequeue($listener);
            }
        }

        return $this;
    }

    /**
     * Get a list of listeners for a specific event
     *
     * @param   string  $name The event name
     * @return  KObjectQueue An object queue containing the listeners
     */
    public function getListeners($name)
    {
        $result = array();
        if(isset($this->_listeners[$name])) {
            $result = $this->_listeners[$name];
        }

        return $result;
    }

    /**
     * Check if we are listening to a specific event
     *
     * @param   string  $name The event name
     * @return  boolean	TRUE if we are listening for a specific event, otherwise FALSE.
     */
    public function hasListeners($name)
    {
        $result = false;
        if(isset($this->_listeners[$name])) {
             $result = (boolean) count($this->_listeners[$name]);
        }

        return $result;
    }

    /**
     * Set the priority of an event
     *
     * @param  string            $name     The event name
     * @param  KObjectHandlable  $listener  An object implementing the KObjectHandlable interface
     * @param  integer           $priority The event priority
     * @return KEventDispatcher
     */
    public function setEventPriority($name, KObjectHandlable $listener, $priority)
    {
        if(isset($this->_listeners[$name])) {
            $this->_listeners[$name]->setPriority($listener, $priority);
        }

        return $this;
    }

    /**
     * Get the priority of an event
     *
     * @param   string            $name     The event name
     * @param   KObjectHandlable  $listener An object implementing the KObjectHandlable interface
     * @return  integer|boolean The event priority or FALSE if the event isn't listened for.
     */
    public function getEventPriority($name, KObjectHandlable $listener)
    {
        $result = false;

        if(isset($this->_listeners[$name])) {
            $result = $this->_listeners[$name]->getPriority($listener);
        }

        return $result;
    }
}

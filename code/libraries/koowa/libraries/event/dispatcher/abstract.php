<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright	Copyright (C) 2007 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/koowa for the canonical source repository
 */

/**
 * Abstract Event Dispatcher
 *
 * API interface inspired upon the DOM Level 2 Event spec and Symfony 2 EventDispatcher component. Implementation
 * provides a priority based event capturing approach. Higher priority event listeners are called first.
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Koowa\Library\Event
 */
abstract class KEventDispatcherAbstract extends KObject implements KEventDispatcherInterface
{
    /**
     * List of event listeners
     *
     * An associative array of event listeners queues where keys are holding the event name and the value
     * is an ObjectQueue object.
     *
     * @var array
     */
    protected $_listeners;

    /**
     * List of event subscribers
     *
     * Associative array of subscribers, where key holds the subscriber handle and the value the subscriber
     * object.
     *
     * @var array
     */
    protected $_subscribers;

    /**
     * Constructor.
     *
     * @param KObjectConfig $config  An optional ObjectConfig object with configuration options
     */
    public function __construct(KObjectConfig $config)
    {
        parent::__construct($config);

        $this->_subscribers = array();
        $this->_listeners   = array();
    }

    /**
     * Dispatches an event by dispatching arguments to all listeners that handle the event.
     *
     * @param   string         $name  The event name
     * @param   object|array   $event An array, a ObjectConfig or a Event object
     * @return  KEventInterface
     */
    public function dispatch($name, $event = array())
    {
        $result = array();

        //Make sure we have an event object
        if (!$event instanceof KEventInterface) {
            $event = new KEvent($event);
        }

        $event->setName($name)
            ->setDispatcher($this);

        //Notify the listeners
        $listeners = $this->getListeners($name);

        foreach ($listeners as $listener)
        {
            call_user_func($listener, $event);

            if (!$event->canPropagate()) {
                break;
            }
        }

        return $event;
    }

    /**
     * Add an event listener
     *
     * @param  string    $name       The event name
     * @param  callable  $listener   The listener
     * @param  integer   $priority   The event priority, usually between 1 (high priority) and 5 (lowest),
     *                               default is 3. If no priority is set, the command priority will be used
     *                               instead.
     * @throws InvalidArgumentException If the listener is not a callable
     * @return KEventDispatcherAbstract
     */
    public function addListener($name, $listener, $priority = KEvent::PRIORITY_NORMAL)
    {
        if (!is_callable($listener))
        {
            throw new InvalidArgumentException(
                'The listener must be a callable, "'.gettype($listener).'" given.'
            );
        }

        $this->_listeners[$name][$priority][] = $listener;

        ksort($this->_listeners[$name]);
        return $this;
    }

    /**
     * Remove an event listener
     *
     * @param   string    $name      The event name
     * @param   callable  $listener  The listener
     * @throws  InvalidArgumentException If the listener is not a callable
     * @return  KEventDispatcherAbstract
     */
    public function removeListener($name, $listener)
    {
        if (!is_callable($listener))
        {
            throw new InvalidArgumentException(
                'The listener must be a callable, "'.gettype($listener).'" given.'
            );
        }

        if (isset($this->_listeners[$name]))
        {
            foreach ($this->_listeners[$name] as $priority => $listeners)
            {
                if (false !== ($key = array_search($listener, $listeners))) {
                    unset($this->_listeners[$name][$priority][$key]);
                }
            }
        }

        return $this;
    }

    /**
     * Get a list of listeners for a specific event
     *
     * @param   string  $name  The event name
     * @return  KObjectQueue   An object queue containing the listeners
     */
    public function getListeners($name)
    {
        $result = array();
        if (isset($this->_listeners[$name]))
        {
            foreach($this->_listeners[$name] as $priority => $listeners) {
                $result = array_merge($result, $listeners);
            }
        }

        return $result;
    }

    /**
     * Check if we are listening to a specific event
     *
     * @param   string  $name The event name
     * @return  boolean  TRUE if we are listening for a specific event, otherwise FALSE.
     */
    public function hasListeners($name)
    {
        $result = false;
        if (isset($this->_listeners[$name])) {
            $result = (boolean)count($this->_listeners[$name]);
        }

        return $result;
    }

    /**
     * Add an event subscriber
     *
     * @param  KEventSubscriberInterface $subscriber The event subscriber to add
     * @param  integer   $priority   The event priority, usually between 1 (high priority) and 5 (lowest),
     *                               default is 3. If no priority is set, the command priority will be used
     *                               instead.
     * @return  KEventDispatcherAbstract
     */
    public function addSubscriber(KEventSubscriberInterface $subscriber, $priority = null)
    {
        $handle = $subscriber->getHandle();

        if (!isset($this->_subscribers[$handle]))
        {
            $listeners = $subscriber->getListeners();
            $priority = is_int($priority) ? $priority : $subscriber->getPriority();

            foreach ($listeners as $name => $listener)
            {
                $listener = $listener['listener'];

                if(!is_int($priority)) {
                    $priority = isset($listener['priority']) ? $listener['priority'] : $subscriber->getPriority();
                }

                $this->addListener($name, $listener, $priority);
            }

            $this->_subscribers[$handle] = $subscriber;
        }

        return $this;
    }

    /**
     * Remove an event subscriber
     *
     * @param  KEventSubscriberInterface $subscriber The event subscriber to remove
     * @return KEventDispatcherAbstract
     */
    public function removeSubscriber(KEventSubscriberInterface $subscriber)
    {
        $handle = $subscriber->getHandle();

        if (isset($this->_subscribers[$handle]))
        {
            $subscriptions = $subscriber->getListeners();

            foreach ($subscriptions as $name => $listener) {
                $this->removeListener($name, $listener);
            }

            unset($this->_subscribers[$handle]);
        }

        return $this;
    }

    /**
     * Gets the event subscribers
     *
     * @return array    An associative array of event subscribers, keys are the subscriber handles
     */
    public function getSubscribers()
    {
        return $this->_subscribers;
    }

    /**
     * Check if the handler is connected to a dispatcher
     *
     * @param  KEventSubscriberInterface $subscriber  The event subscriber
     * @return boolean TRUE if the handler is already connected to the dispatcher. FALSE otherwise.
     */
    public function isSubscribed(KEventSubscriberInterface $subscriber)
    {
        $handle = $subscriber->getHandle();
        return isset($this->_subscribers[$handle]);
    }

    /**
     * Set the priority of an event
     *
     * @param  string    $name      The event name
     * @param  callable  $listener  The listener
     * @param  integer   $priority  The event priority
     * @throws  InvalidArgumentException If the listener is not a callable
     * @return  KEventDispatcherAbstract
     */
    public function setPriority($name, $listener, $priority)
    {
        if (!is_callable($listener))
        {
            throw new InvalidArgumentException(
                'The listener must be a callable, "'.gettype($listener).'" given.'
            );
        }

        if ($this->hasListeners($name))
        {
            foreach ($this->getListeners($name) as $priority => $listeners)
            {
                if (false !== ($key = array_search($listener, $listeners)))
                {
                    unset($this->_listeners[$name][$priority][$key]);
                    $this->_listeners[$name][$priority][] = $listener;
                }
            }
        }

        return $this;
    }

    /**
     * Get the priority of an event
     *
     * @param   string    $name      The event name
     * @param   callable  $listener  The listener
     * @throws  InvalidArgumentException If the listener is not a callable
     * @return  integer|false The event priority or FALSE if the event isn't listened for.
     */
    public function getPriority($name, $listener)
    {
        $result = false;

        if (!is_callable($listener))
        {
            throw new InvalidArgumentException(
                'The listener must be a callable, "'.gettype($listener).'" given.'
            );
        }

        if ($this->hasListeners($name))
        {
            foreach ($this->getListeners($name) as $priority => $listeners)
            {
                if (false !== ($key = array_search($listener, $listeners)))
                {
                    $result = $priority;
                    break;
                }
            }
        }

        return $result;
    }
}

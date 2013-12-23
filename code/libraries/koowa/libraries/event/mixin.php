<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright	Copyright (C) 2007 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/koowa for the canonical source repository
 */

/**
 * Event Mixin
 *
 * Class can be used as a mixin in classes that want to implement a an event dispatcher and allow adding and removing
 * listeners.
 *
 * @author  Johan Janssens <http://github.com/johanjanssens>
 * @package Koowa\Library\Event
 */
class KEventMixin extends KObjectMixinAbstract
{
    /**
     * Event dispatcher object
     *
     * @var KEventDispatcherInterface
     */
    protected $_event_dispatcher;

    /**
     * List of event subscribers
     *
     * Associative array of event subscribers, where key holds the subscriber identifier string
     * and the value is an identifier object.
     *
     * @var    array
     */
    protected $_event_subscribers = array();

    /**
     * Object constructor
     *
     * @param KObjectConfig $config An optional ObjectConfig object with configuration options
     * @throws InvalidArgumentException
     */
    public function __construct(KObjectConfig $config)
    {
        parent::__construct($config);

        if (is_null($config->event_dispatcher)) {
            throw new InvalidArgumentException('event_dispatcher [KEventDispatcherInterface] config option is required');
        }

        //Set the event dispatcher
        $this->_event_dispatcher = $config->event_dispatcher;

        //Add the event listeners
        foreach ($config->event_listeners as $event => $listener) {
            $this->addEventListener($event, $listener);
        }

        //Add the event handlers
        $subscribers = (array) KObjectConfig::unbox($config->event_subscribers);

        foreach ($subscribers as $key => $value)
        {
            if (is_numeric($key)) {
                $this->addEventSubscriber($value);
            } else {
                $this->addEventSubscriber($key, $value);
            }
        }
    }

    /**
     * Initializes the options for the object
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param   KObjectConfig $config  An optional ObjectConfig object with configuration options
     * @return  void
     */
    protected function _initialize(KObjectConfig $config)
    {
        $config->append(array(
            'event_dispatcher'  => 'event.dispatcher',
            'event_subscribers' => array(),
            'event_listeners'   => array(),
        ));

        parent::_initialize($config);
    }

    /**
     * Get the event dispatcher
     *
     * @throws UnexpectedValueException
     * @return  KEventDispatcherInterface
     */
    public function getEventDispatcher()
    {
        if(!$this->_event_dispatcher instanceof KEventDispatcherInterface)
        {
            $this->_event_dispatcher = $this->getObject($this->_event_dispatcher);

            //Make sure the request implements KEventDispatcherInterface
            if(!$this->_event_dispatcher instanceof KEventDispatcherInterface)
            {
                throw new UnexpectedValueException(
                    'EventDispatcher: '.get_class($this->_event_dispatcher).' does not implement KEventDispatcherInterface'
                );
            }
        }

        return $this->_event_dispatcher;
    }

    /**
     * Set the chain of command object
     *
     * @param   KEventDispatcherInterface  $dispatcher An event dispatcher object
     * @return  Object  The mixer object
     */
    public function setEventDispatcher(KEventDispatcherInterface $dispatcher)
    {
        $this->_event_dispatcher = $dispatcher;
        return $this->getMixer();
    }

    /**
     * Add an event listener
     *
     * @param  string   $event     The event name
     * @param  callable $listener  The listener
     * @param  integer $priority   The event priority, usually between 1 (high priority) and 5 (lowest),
     *                             default is 3. If no priority is set, the command priority will be used
     *                             instead.
     * @return  Object The mixer objects
     */
    public function addEventListener($event, $listener, $priority = KEventInterface::PRIORITY_NORMAL)
    {
        $this->getEventDispatcher()->addListener($event, $listener, $priority);
        return $this->getMixer();
    }

    /**
     * Remove an event listener
     *
     * @param   string   $event     The event name
     * @param   callable $listener  The listener
     * @return  Object  The mixer object
     */
    public function removeEventListener($event, $listener)
    {
        $this->getEventDispatcher()->removeListener($event, $listener);
        return $this->getMixer();
    }

    /**
     * Add an event subscriber
     *
     * @param   mixed  $subscriber An object that implements ObjectInterface, ObjectIdentifier object
     *                            or valid identifier string
     * @param  array  $config   An optional associative array of configuration settings
     * @param  integer $priority The event priority, usually between 1 (high priority) and 5 (lowest),
     *                 default is 3. If no priority is set, the command priority will be used
     *                 instead.
     * @return  Object    The mixer object
     */
    public function addEventSubscriber($subscriber, $config = array(), $priority = null)
    {
        if (!($subscriber instanceof KEventSubscriberInterface)) {
            $subscriber = $this->getEventSubscriber($subscriber, $config);
        }

        $priority = is_int($priority) ? $priority : $subscriber->getPriority();
        $this->getEventDispatcher()->addSubscriber($subscriber, $priority);

        return $this;
    }

    /**
     * Remove an event subscriber
     *
     * @param   mixed  $subscriber An object that implements ObjectInterface, ObjectIdentifier object
     *                             or valid identifier string
     * @return  Object  The mixer object
     */
    public function removeEventSubscriber($subscriber)
    {
        if (!($subscriber instanceof KEventSubscriberInterface)) {
            $subscriber = $this->getEventSubscriber($subscriber);
        }

        $this->getEventDispatcher()->removeSubscriber($subscriber);
        return $this->getMixer();
    }

    /**
     * Get a event subscriber by identifier
     *
     * @param  mixed $subscriber An object that implements ObjectInterface, ObjectIdentifier object
     *                          or valid identifier string
     * @param  array  $config   An optional associative array of configuration settings
     * @throws UnexpectedValueException    If the subscriber is not implementing the EventSubscriberInterface
     * @return KEventSubscriberInterface
     */
    public function getEventSubscriber($subscriber, $config = array())
    {
        if (!($subscriber instanceof KObjectIdentifier))
        {
            //Create the complete identifier if a partial identifier was passed
            if (is_string($subscriber) && strpos($subscriber, '.') === false)
            {
                $identifier = clone $this->getIdentifier();
                $identifier->path = array('event', 'subscriber');
                $identifier->name = $subscriber;
            }
            else $identifier = $this->getIdentifier($subscriber);
        }
        else $identifier = $subscriber;

        if (!isset($this->_event_subscribers[(string)$identifier]))
        {
            $config['event_dispatcher'] = $this->getEventDispatcher();
            $subscriber = $this->getObject($identifier, $config);

            //Check the event subscriber interface
            if (!($subscriber instanceof KEventSubscriberInterface))
            {
                throw new UnexpectedValueException(
                    "Event Subscriber $identifier does not implement KEventSubscriberInterface"
                );
            }
        }
        else $subscriber = $this->_event_subscribers[(string)$identifier];

        return $subscriber;
    }
}
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
 * Event Subscriber
 *
 * An EventSusbcriber knows himself what events he is interested in. Classes extending the abstract implementation may
 * be adding listeners to an EventDispatcher through the {@link subscribe()} method.
 *
 * Listeners must be public class methods following a camel Case naming convention starting with 'on', eg onFooBar. The
 * listener priority is usually between 1 (high priority) and 5 (lowest), default is 3 (normal)
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Kodekit\Library\Event\Subscriber
 */
abstract class EventSubscriberAbstract extends ObjectAbstract implements EventSubscriberInterface, ObjectMultiton
{
    /**
     * List of subscribed listeners
     *
     * @var array
     */
    private $__publishers;

    /**
     * The subscriber priority
     *
     * @var integer
     */
    protected $_priority;

    /**
     * Constructor.
     *
     * @param ObjectConfig $config  An optional ObjectConfig object with configuration options
     */
    public function __construct(ObjectConfig $config)
    {
        parent::__construct($config);

        //Set the command priority
        $this->_priority = $config->priority;
    }

    /**
     * Initializes the options for the object
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param  ObjectConfig $config A ObjectConfig object with configuration options
     * @return void
     */
    protected function _initialize(ObjectConfig $config)
    {
        $config->append(array(
            'priority'   => self::PRIORITY_NORMAL,
            'enabled'    => true,
        ));

        parent::_initialize($config);
    }

    /**
     * Attach one or more listeners
     *
     * Event listeners always start with 'on' and need to be public methods.
     *
     * @param EventPublisherInterface $publisher
     * @return array An array of public methods that have been attached
     */
    public function subscribe(EventPublisherInterface $publisher)
    {
        $handle    = $publisher->getHandle();
        $listeners = [];

        if($this->isEnabled() && !$this->isSubscribed($publisher))
        {
            $listeners = $this->getEventListeners();

            foreach ($listeners as $listener)
            {
                $publisher->addListener($listener, array($this, $listener), $this->getPriority());
                $this->__publishers[$handle][] = $listener;
            }
        }

        return $listeners;
    }

    /**
     * Get the priority of a subscriber
     *
     * @return integer The subscriber priority
     */
    public function getPriority()
    {
        return $this->_priority;
    }

    /**
     * Detach all previously attached listeners for the specific dispatcher
     *
     * @param EventPublisherInterface $publisher
     * @return void
     */
    public function unsubscribe(EventPublisherInterface $publisher)
    {
        $handle = $publisher->getHandle();

        if($this->isSubscribed($publisher))
        {
            foreach ($this->__publishers[$handle] as $index => $listener)
            {
                $publisher->removeListener($listener, array($this, $listener));
                unset($this->__publishers[$handle][$index]);
            }
        }
    }

    /**
     * Check if the subscriber is already subscribed to the dispatcher
     *
     * @param  EventPublisherInterface $publisher  The event dispatcher
     * @return boolean TRUE if the subscriber is already subscribed to the dispatcher. FALSE otherwise.
     */
    public function isSubscribed(EventPublisherInterface $publisher)
    {
        $handle = $publisher->getHandle();
        return isset($this->__publishers[$handle]);
    }

    /**
     * Check if the subscriber is enabled
     *
     * @return boolean TRUE if the subscriber is enabled. FALSE otherwise.
     */
    public function isEnabled()
    {
        return $this->getConfig()->enabled;
    }

    /**
     * Get the event listeners
     *
     * @return array
     */
    public static function getEventListeners()
    {
        $listeners = array();

        $reflection = new \ReflectionClass(get_called_class());
        foreach ($reflection->getMethods(\ReflectionMethod::IS_PUBLIC) as $method)
        {
            if(substr($method->name, 0, 2) == 'on') {
                $listeners[] = $method->name;
            }
        }

        return $listeners;
    }
}

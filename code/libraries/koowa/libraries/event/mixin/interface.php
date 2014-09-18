<?php
/**
 * Nooku Framework - http://nooku.org/framework
 *
 * @copyright   Copyright (C) 2007 - 2014 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/nooku/nooku-framework for the canonical source repository
 */

/**
 * Event Mixin Interface
 *
 * Class can be used as a mixin in classes that want to implement a an event publisher and allow adding and removing
 * event listeners and subscribers.
 *
 * @author  Johan Janssens <http://github.com/johanjanssens>
 * @package Koowa\Library\Event\Mixin
 */
interface KEventMixinInterface
{
    /**
     * Publish an event by calling all listeners that have registered to receive it.
     *
     * @param  string|KEventInterface  $event     The event name or a KEventInterface object
     * @param  array|Traversable       $attributes An associative array or a Traversable object
     * @param  KObjectInterface        $target    The event target
     * @return null|KEventInterface Returns the event object. If the chain is not enabled will return NULL.
     */
    public function publishEvent($event, $attributes = array(), $target = null);

    /**
     * Get the event publisher
     *
     * @throws UnexpectedValueException
     * @return  KEventPublisherInterface
     */
    public function getEventPublisher();

    /**
     * Set the event publisher
     *
     * @param   KEventPublisherInterface  $publisher An event publisher object
     * @return  Object  The mixer object
     */
    public function setEventPublisher(KEventPublisherInterface $publisher);

    /**
     * Add an event listener
     *
     * @param string|KEventInterface  $event     The event name or a KEventInterface object
     * @param callable                $listener  The listener
     * @param integer                 $priority  The event priority, usually between 1 (high priority) and 5 (lowest),
     *                                            default is 3 (normal)
     * @throws InvalidArgumentException If the listener is not a callable
     * @throws InvalidArgumentException  If the event is not a string or does not implement the KEventInterface
     * @return KObjectInterface The mixer
     */
    public function addEventListener($event, $listener, $priority = KEventInterface::PRIORITY_NORMAL);

    /**
     * Remove an event listener
     *
     * @param string|KEventInterface  $event     The event name or a KEventInterface object
     * @param callable                $listener  The listener
     * @throws InvalidArgumentException If the listener is not a callable
     * @throws InvalidArgumentException  If the event is not a string or does not implement the KEventInterface
     * @return KObjectInterface The mixer
     */
    public function removeEventListener($event, $listener);

    /**
     * Add an event subscriber
     *
     * @param mixed  $subscriber An object that implements ObjectInterface, ObjectIdentifier object
     *                           or valid identifier string
     * @param array  $config     An optional associative array of configuration options
     * @return KObjectInterface The mixer
     */
    public function addEventSubscriber($subscriber, $config = array());

    /**
     * Remove an event subscriber
     *
     * @param   KEventSubscriberInterface  $subscriber An event subscriber
     * @return  Object  The mixer object
     */
    public function removeEventSubscriber(KEventSubscriberInterface $subscriber);

    /**
     * Get a event subscriber by identifier
     *
     * The subscriber will be created if does not exist yet, otherwise the existing subscriber will be returned.
     *
     * @param  mixed $subscriber An object that implements ObjectInterface, ObjectIdentifier object
     *                          or valid identifier string
     * @param  array  $config   An optional associative array of configuration settings
     * @throws UnexpectedValueException    If the subscriber is not implementing the EventSubscriberInterface
     * @return KEventSubscriberInterface
     */
    public function getEventSubscriber($subscriber, $config = array());

    /**
     * Gets the event subscribers
     *
     * @return array An array of event subscribers
     */
    public function getEventSubscribers();
}
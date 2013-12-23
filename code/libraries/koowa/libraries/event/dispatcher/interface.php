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
 * API interface inspired upon the DOM Level 2 Event spec and Symfony 2 EventDispatcher component. Implementation
 * provides a priority based event capturing approach. Higher priority event listeners are called first.
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Koowa\Library\Event
 */
interface KEventDispatcherInterface
{
    /**
     * Dispatches an event by dispatching arguments to all listeners that handle the event.
     *
     * @param   string         $name  The event name
     * @param   object|array   $event An array, a ObjectConfig or a Event object
     * @return  KEventInterface
     */
    public function dispatch($name, $event = array());

    /**
     * Add an event listener
     *
     * @param  string    $name       The event name
     * @param  callable  $listener   The listener
     * @param  integer   $priority   The event priority, usually between 1 (high priority) and 5 (lowest),
     *                               default is 3. If no priority is set, the command priority will be used
     *                               instead.
     * @return KEventDispatcherInterface
     */
    public function addListener($name, $listener, $priority = KEvent::PRIORITY_NORMAL);

    /**
     * Remove an event listener
     *
     * @param   string    $name      The event name
     * @param   callable  $listener  The listener
     * @return  KEventDispatcherInterface
     */
    public function removeListener($name, $listener);

    /**
     * Get a list of listeners for a specific event
     *
     * @param   string  $name  The event name
     * @return  KObjectQueue    An object queue containing the listeners
     */
    public function getListeners($name);

    /**
     * Check if we are listening to a specific event
     *
     * @param   string  $name The event name
     * @return  boolean  TRUE if we are listening for a specific event, otherwise FALSE.
     */
    public function hasListeners($name);

    /**
     * Add an event subscriber
     *
     * @param  KEventSubscriberInterface $subscriber The event subscriber to add
     * @param  integer   $priority   The event priority, usually between 1 (high priority) and 5 (lowest),
     *                               default is 3. If no priority is set, the command priority will be used
     *                               instead.
     * @return  KEventDispatcherInterface
     */
    public function addSubscriber(KEventSubscriberInterface $subscriber, $priority = null);

    /**
     * Remove an event subscriber
     *
     * @param  KEventSubscriberInterface $subscriber The event subscriber to remove
     * @return  KEventDispatcherInterface
     */
    public function removeSubscriber(KEventSubscriberInterface $subscriber);

    /**
     * Gets the event subscribers
     *
     * @return array    An associative array of event subscribers, keys are the subscriber handles
     */
    public function getSubscribers();

    /**
     * Check if the handler is connected to a dispatcher
     *
     * @param KEventSubscriberInterface $subscriber  The event dispatcher
     * @return boolean TRUE if the handler is already connected to the dispatcher. FALSE otherwise.
     */
    public function isSubscribed(KEventSubscriberInterface $subscriber);

    /**
     * Set the priority of an event
     *
     * @param  string   $name      The event name
     * @param  callable $listener  The listener
     * @param  integer  $priority  The event priority
     * @return  KEventDispatcherInterface
     */
    public function setPriority($name, $listener, $priority);

    /**
     * Get the priority of an event
     *
     * @param   string    $name      The event name
     * @param   callable  $listener  The listener
     * @return  integer|false The event priority or FALSE if the event isn't listened for.
     */
    public function getPriority($name, $listener);
}

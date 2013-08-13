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
interface KEventDispatcherInterface
{
 	/**
     * Dispatches an event by dispatching arguments to all listeners that handle the event and returning
     * their return values.
     *
     * @param   string  $name  The event name
     * @param   object|array   An array, a KConfig or a KEvent object
     * @return  KEventDispatcher
     */
    public function dispatchEvent($name, $event = array());

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
    public function addEventListener($name, KObjectHandlable $listener, $priority = KEvent::PRIORITY_NORMAL);

    /**
     * Remove an event listener
     *
     * @param   string           $name      The event name
     * @param   KObjectHandlable $listener  An object implementing the KObjectHandlable interface
     * @return  KEventDispatcher
     */
    public function removeEventListener($name, KObjectHandlable $listener);

    /**
     * Get a list of listeners for a specific event
     *
     * @param   string  $name The event name
     * @return  KObjectQueue An object queue containing the listeners
     */
    public function getListeners($name);

    /**
     * Check if we are listening to a specific event
     *
     * @param   string  $name The event name
     * @return  boolean	TRUE if we are listening for a specific event, otherwise FALSE.
     */
    public function hasListeners($name);

	/**
     * Set the priority of an event
     *
     * @param  string            $name     The event name
     * @param  KObjectHandlable  $listener  An object implementing the KObjectHandlable interface
     * @param  integer           $priority The event priority
     * @return KCommandChain
     */
    public function setEventPriority($name, KObjectHandlable $listener, $priority);

    /**
     * Get the priority of an event
     *
     * @param   string            $name     The event name
     * @param   KObjectHandlable  $listener An object implementing the KObjectHandlable interface
     * @return  integer|false The event priority or FALSE if the event isn't listened for.
     */
    public function getEventPriority($name, KObjectHandlable $listener);
}

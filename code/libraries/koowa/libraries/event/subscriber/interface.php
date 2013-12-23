<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright	Copyright (C) 2007 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/koowa for the canonical source repository
 */

/**
 * Event Subscriber Interface
 *
 * An EventSubscriber knows himself what events he is interested in. If an EventSubscriber is added to an
 * EventDispatcherInterface, the dispatcher invokes {@link getListeners} and registers the subscriber
 * as a listener for all returned events.
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Koowa\Library\Event
 */
interface KEventSubscriberInterface
{
    /**
     * Get the priority of the subscriber
     *
     * @return	integer The event priority
     */
    public function getPriority();

    /**
     * Get a list of subscribed events
     *
     * The array keys are event names and the value is an associative array composed of a callable and an optional
     * priority. If no priority is defined the dispatcher is responsible to set a default.
     *
     * eg  array('eventName' => array('calla' => array($object, 'methodName'), 'priority' => $priority))
     *
     * @return array The event names to listen to
     */
    public function getListeners();
}

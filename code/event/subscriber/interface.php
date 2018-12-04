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
 * Event Subscriber Interface
 *
 * An EventSusbcriber knows himself what events he is interested in. Classes implementing this interface may be adding
 * listeners to an EventDispatcher through the {@link subscribe()} method.
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Kodekit\Library\Event\Subscriber
 */
interface EventSubscriberInterface
{
    /**
     * Register one or more listeners
     *
     * @param EventPublisherInterface $publisher
     * @param  integer                 $priority   The event priority, usually between 1 (high priority) and 5 (lowest),
     *                                 default is 3 (normal)
     * @@return array An array of public methods that have been attached
     */
    public function subscribe(EventPublisherInterface $publisher, $priority = EventInterface::PRIORITY_NORMAL);

    /**
     * Unsubscribe all previously registered listeners
     *
     * @param EventPublisherInterface $publisher The event dispatcher
     * @return void
     */
    public function unsubscribe(EventPublisherInterface $publisher);

    /**
     * Check if the subscriber is already subscribed to the dispatcher
     *
     * @param  EventPublisherInterface $publisher  The event dispatcher
     * @return boolean TRUE if the subscriber is already subscribed to the dispatcher. FALSE otherwise.
     */
    public function isSubscribed(EventPublisherInterface $publisher);

    /**
     * Get the event listeners
     *
     * @return array
     */
    public static function getEventListeners();
}

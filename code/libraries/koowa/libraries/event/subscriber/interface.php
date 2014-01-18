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
 * An EventSusbcriber knows himself what events he is interested in. Classes implementing this interface may be adding
 * listeners to an EventDispatcher through the {@link subscribe()} method.
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Koowa\Library\Event
 */
interface KEventSubscriberInterface
{
    /**
     * Register one or more listeners
     *
     * @param KEventPublisherInterface $publisher
     * @param  integer                 $priority   The event priority, usually between 1 (high priority) and 5 (lowest),
     *                                 default is 3 (normal)
     * @@return array An array of public methods that have been attached
     */
    public function subscribe(KEventPublisherInterface $publisher, $priority = KEventInterface::PRIORITY_NORMAL);

    /**
     * Unsubscribe all previously registered listeners
     *
     * @param KEventPublisherInterface $dispatcher The event dispatcher
     * @return void
     */
    public function unsubscribe(KEventPublisherInterface $publisher);

    /**
     * Check if the subscriber is already subscribed to the dispatcher
     *
     * @param  KEventPublisherInterface $dispatcher  The event dispatcher
     * @return boolean TRUE if the subscriber is already subscribed to the dispatcher. FALSE otherwise.
     */
    public function isSubscribed(KEventPublisherInterface $publisher);
}

<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright	Copyright (C) 2007 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/koowa for the canonical source repository
 */

/**
 * Event Listener Interface
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Koowa\Library\Event
 */
interface KEventListenerInterface
{
    /**
     * Get the event handlers of the listener
     *
     * Event handlers always start with 'on' and need to be public methods
     *
     * @return array An array of public methods
     */
    public function getEventHandlers();

    /**
     * Connect to an event dispatcher
     *
     * @param  KEventDispatcher $dispatcher	The event dispatcher to connect too
     * @return KEventListener
     */
    public function connect(KEventDispatcher $dispatcher);

	/**
     * Disconnect from an event dispatcher
     *
     * @param  KEventDispatcher $dispatcher	The event dispatcher to disconnect from
     * @return KEventListener
     */
    public function disconnect(KEventDispatcher $dispatcher);
}

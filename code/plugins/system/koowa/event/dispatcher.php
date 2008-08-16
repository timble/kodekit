<?php
/**
 * @version     $Id$
 * @package     Koowa_Event
 * @copyright   Copyright (C) 2007 - 2008 Joomlatools. All rights reserved.
 * @license     GNU GPLv2 <http://www.gnu.org/licenses/old-licenses/gpl-2.0.html>
 * @link        http://www.koowa.org
 */

/**
 * Class to handle dispatching of events.
 *
 * @author Johan Janssens <johan@joomlatools.org>
 * @package Koowa_Event
 */
class KEventDispatcher extends KPatternObservable
{
	/**
	 * Registers an event handler to the event dispatcher
	 *
	 * @param	string	$handler	Name of the event handler
	 */
	public function register($handler)
	{
		if (class_exists($handler)) {
			$this->attach(new $handler($this));
		}
	}

	/**
	 * Triggers an event by dispatching arguments to all observers that handle
	 * the event and returning their return values.
	 *
	 * @param	string	$event			The event name
	 * @param	array	$args			An array of arguments
	 * @return	array	An array of results from each function call
	 */
	public function trigger($event, $args = array())
	{
		// Initialize variables
		$result = array ();

		/*
		 * Iterate through all of the registered observers and trigger the event for 
		 * each observer that handles the event.
		 */
		foreach ($this->_observers as $observer)
		{
			if (method_exists($observer, $event)) {
				$result[] = $observer->update($event, $args);
			}
		}
		return $result;
	}
}

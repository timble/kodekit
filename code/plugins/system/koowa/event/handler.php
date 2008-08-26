<?php
/**
 * @version     $Id$
 * @package     Koowa_Event
 * @copyright   Copyright (C) 2007 - 2008 Joomlatools. All rights reserved.
 * @license     GNU GPLv2 <http://www.gnu.org/licenses/old-licenses/gpl-2.0.html>
 * @link        http://www.koowa.org
 */

/**
 * Class to handle events.
 *
 * @author Johan Janssens <johan@joomlatools.org>
 * @package Koowa_Event
 * @uses 	KObserver
 */
class KEventHandler extends KObserver
{
	/**
	 * Method to trigger events
	 *
	 * @param array Arguments
	 * @return mixed Routine return value
	 */
	public function onNotify($event, $args = array())
	{
		if (method_exists($this, $event)) {
			return call_user_func_array ( array($this, $event), $args );
		} 
		
		return null;
	}
}

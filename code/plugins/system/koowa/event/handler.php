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
class KEventHandler extends KPatternObserver
{
	/**
	 * Method to trigger events
	 *
	 * @param  object	$args	 The event arguments
	 * @return mixed Routine return value
	 */
	public function onNotify(ArrayObject $args)
	{
		if (method_exists($this, $args['event'])) {
			return call_user_func_array ( array($this, $args['event']), $args );
		} 
		
		return null;
	}
}

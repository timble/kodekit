<?php
/**
 * @version		$Id$
 * @category    Koowa
 * @package     Koowa_Plugins
 * @subpackage  Koowa
 * @copyright	Copyright (C) 2007 - 2008 Joomlatools. All rights reserved.
 * @license		GNU GPLv2 <http://www.gnu.org/licenses/old-licenses/gpl-2.0.html>
 * @link     	http://www.nooku.org
 */

/**
 * Koowa Example Event Handler
 *
 * @author		Johan Janssens <johan@joomlatools.org>
 * @category    Koowa
 * @package     Koowa_Plugins
 * @subpackage  Koowa
 */
class plgKoowaExample extends KEventHandler 
{
	/**
	 * Application event handler
	 * 
	 * This event gets triggered before an application task is executed
	 *
	 * @param  object	$args	 The event arguments
	 * @return mixed
	 */
	public function onBeforeApplicationExecute(ArrayObject $args) { }
	
	/**
	 * Application event handler
	 * 
	 * This event gets triggered after an application task is executed
	 *
	 * @param  object	$args	 The event arguments
	 * @return mixed
	 */
	public function onAfterpplicationExecute(ArrayObject $args)   { }
	
	/**
	 * Controller event handler
	 * 
	 * This event gets triggered before a controller task is executed. For performance reasons
	 * display tasks are not handled.
	 *
	 * @param  object	$args	 The event arguments
	 * @return mixed
	 */
	public function onBeforeControllerExecute(ArrayObject $args)  { }
	
	/**
	 * Controller event handler
	 * 
	 * This event gets triggered after a controller task is executed. For performance reasons
	 * display tasks are not handled.
	 *
	 * @param  object	$args	 The event arguments
	 * @return mixed
	 */
	public function onAfterControllerExecute(ArrayObject $args)   { }
	
	/**
	 * Database event handler
	 * 
	 * This event gets triggered before an database operation is executed. For performance reasons
	 * SELECT operations are not handled.
	 *
	 * @param  object	$args	 The event arguments
	 * @return mixed
	 */
	public function onBeforeDatabaseExecute(ArrayObject $args)    { }
	
	/**
	 * Database event handler
	 * 
	 * This event gets triggered after an database operation is executed. For performance reasons
	 * SELECT operations are not handled.
	 *
	 * @param  object	$args	 The event arguments
	 * @return mixed
	 */
	public function onAfterDatabaseExecute(ArrayObject $args)     { }
}
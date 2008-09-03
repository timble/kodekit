<?php
/**
 * @version     $Id:koowa.php 251 2008-06-14 10:06:53Z mjaz $
 * @package     Koowa
 * @copyright   Copyright (C) 2007 - 2008 Joomlatools. All rights reserved.
 * @license     http://www.gnu.org/copyleft/gpl.html GNU/GPLv2
 * @link        http://www.koowa.org
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

/**
 * Koowa System plugin
 *
 * @author		Mathias Verraes <mathias@joomlatools.org>
 * @package		Koowa
 * @version		1.0
 */
class plgSystemKoowa extends JPlugin
{
	public function __construct($subject, $config = array())
	{
		// Require the library loader
		if( self::canEnable()) {	
			require_once dirname(__FILE__).DS.'koowa'.DS.'koowa.php';
			require_once dirname(__FILE__).DS.'koowa'.DS.'loader.php';	
		}
		
		parent::__construct($subject, $config = array());
	}

	public function onAfterInitialise()
	{
		if( ! self::canEnable()) {	
			return;
		}
	
		// Proxy the application object 
		$app  =& JFactory::getApplication();
		$app  = new KApplication($app);
		
		// Proxy the database object
		$db  =& JFactory::getDBO();
		$db  = new KDatabase($db);
	}
	
	/**
	 * Check if the current request requires Koowa to be turned off
	 * 
	 * Eg. Koowa should be disabled when uninstalling plugins
	 *
	 * @return	bool
	 */
	public static function canEnable()
	{
		$result = true;
		
		// Note: can't use KInput, Koowa isn't loaded yet
		
		// are we uninstalling a plugin?
		if(JRequest::getCmd('option') == 'com_installer' 
			&& JRequest::getCmd('task') == 'remove'
			&& JRequest::getCmd('type') == 'plugins' ) {
			$result = false;
		}
		
		return $result;
	}
}
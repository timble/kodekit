<?php
/**
* @version      $Id:koowa.php 251 2008-06-14 10:06:53Z mjaz $
* @category		Koowa
* @package      Koowa
* @copyright    Copyright (C) 2007 - 2008 Joomlatools. All rights reserved.
* @license      GNU GPLv2 <http://www.gnu.org/licenses/old-licenses/gpl-2.0.html>
*/

/**
 * Koowa is active
 */
define('KOOWA', 1);

/**
 * DS is a shortcut for DIRECTORY_SEPARATOR
 */
if(!defined('DS')) {
	define('DS', DIRECTORY_SEPARATOR);
}

/**
 * Koowa class
 *
 * Loads classes and files, and provides metadata for Koowa such as version info
 *
 * @author		Johan Janssens <johan@joomlatools.org>
 * @author		Mathias Verraes <mathias@joomlatools.org>
 * @package     Koowa
 * @version     1.0
 */
class Koowa
{
    /**
     * Koowa version
     */
    const _VERSION = '0.6.0';

    /**
     * Path to Koowa libraries
     */
    protected static $_path;

    /**
     * Get the version of the Koowa library
     */
    public static function getVersion()
    {
   	    return self::_VERSION;
    }

    /**
     * Get path to Koowa libraries
     */
    public static function getPath()
    {
    	if(!isset(self::$_path)) {
        	self::$_path = dirname(__FILE__);
        }

        return self::$_path;
    }
    
  	/**
     * Get the URL to the folder containing all media assets
     *
     * @param string	$type	The type of URL to return, default 'media'
     * @return 	string	URL
     */
    public static function getURL($type = 'media')
    {
    	$url = '';
    	
    	switch($type) 
    	{
    		case 'media' :
    			$url = JURI::root().'media/plg_koowa/';
    			break;
    	}
    	
    	return $url;
    }

	/**
 	 * Intelligent file importer
	 *
 	 * @package		Koowa
 	 * @param string $path A dot syntax path
 	 */
	public static function import( $path, $basepath = '')
	{
		return KLoader::loadFile($path, $basepath);
	}
}
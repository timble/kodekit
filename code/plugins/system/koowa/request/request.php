<?php
/**
 * @version      $Id:koowa.php 251 2008-06-14 10:06:53Z mjaz $
 * @package      Koowa_Request
 * @copyright    Copyright (C) 2007 - 2008 Joomlatools. All rights reserved.
 * @license      GNU GPLv2 <http://www.gnu.org/licenses/old-licenses/gpl-2.0.html>
 */

/**
 * Request class
 *
 * Allows to get input from GET, POST, PUT, DELETE, COOKIE, ENV, SERVER
 *
 * @author		Mathias Verraes <mathias@joomlatools.org>
 * @package     Koowa_Request
 * @version     1.0
 */
class KRequest
{
	protected static $_is_initialized;
	
	/**
	 * Input from all sources
	 *
	 * @var	array
	 */
	protected static $_input = array();
	
	/**
	 * List of accepted hashes
	 *
	 * @var	array
	 */
	protected static $_hashes = array('COOKIE', 'DELETE', 'ENV', 'FILES', 'GET', 'POST', 'PUT', 'SERVER');
	
	/**
	 * Get a validated and optionally sanitized variable from the request. 
	 *
	 * @throws	KRequestException	When the variable doesn't validate
	 * 
	 * @param	string	Variable name
	 * @param 	string  Hash [GET|POST|PUT|DELETE|COOKIE|ENV|SERVER]
	 * @param 	mixed	Validator(s), can be a KFilterInterface object, or array of objects or classnames 
	 * @param 	mixed	Sanitizer(s), can be a KFilterInterface object, classname, or array of objects or classnames
	 * @param 	mixed	Default value when the variable doesn't exist
	 * @return 	mixed	(Sanitized) variable
	 */
	public static function get($var, $hash, $validators, $sanitizers = array(), $default = null)
	{
		if(!isset(self::$_is_initialized)) {
			self::_initialize();
		}
		
		
		// Is the hash in our list?
		$hash = strtoupper($hash);
		if(!in_array($hash, self::$_hashes)) {
			throw new KRequestException('Unknown hash: '.$hash);
		}		
		
		// return the default value if $var wasn't set in the request
		if(!isset(self::$_input[$hash][$var])) {
			return $default; 	
		}
		$result = self::$_input[$hash][$var];
		
		// $validators and $sanitizers can be strings (classnames), objects (instances of the filters), or arrays (of mixed classnames and instances)
		if(!is_array($validators)) {
			$validators = array($validators);
		}
		if(!is_array($sanitizers)) {
			$sanitizers = array($sanitizers);
		}
		
		// validate the variable
		foreach($validators as $filter)
		{
			if(!is_object($filter)) {
				$filter = new $filter;
			}
			if(!($filter instanceof KFilterInterface)) {
				throw new KRequestException('Invalid filter passed');
			}
			if(!$filter->validate($result)) 
			{
				$filtername = KInflector::getPart(get_class($filter), -1);
				throw new KRequestException('Input is not a valid '.$filtername);
			}			 
		}
		
		// sanitize the variable
		foreach($sanitizers as $filter)
		{
			if(!is_object($filter)) {
				$filter = new $filter;
			}
			if(!($filter instanceof KFilterInterface)) {
				throw new KRequestException('Invalid filter passed');
			}
			$result = $filter->sanitize($result);		 
		}
		
		return $result;
		
	}
	
	/**
	 * Get the request method
	 *
	 * @return 	string
	 */
	public static function getMethod()
	{
		return strtoupper(self::$_input['SERVER']['REQUEST_METHOD']);
	}
	
	/**
	 * Initialize
	 */	
	protected static function _initialize()
	{	
		self::$_input['COOKIE'] = $_COOKIE;
		self::$_input['DELETE'] = array();
		self::$_input['ENV'] 	= $_ENV;
		self::$_input['FILES'] 	= $_FILES;
		self::$_input['GET']	= $_GET;
		self::$_input['POST'] 	= $_POST;
		self::$_input['PUT'] 	= array();		
		self::$_input['SERVER'] = $_SERVER;
		
		// Get PUT and DELETE from the input stream
		$method = self::getMethod();
		if('PUT' == $method) {
			parse_str(file_get_contents('php://input'), self::$_input['PUT']);
		} elseif('DELETE' == $method) {
			parse_str(file_get_contents('php://input'), self::$_input['DELETE']);	
		}
		
		// TODO: in a koowa-only environment, all input superglobals should be 
		// unset, so that input can only be accessed throug KRequest
	}
	
}
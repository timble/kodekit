<?php
/**
 * @version 	$Id:factory.php 46 2008-03-01 18:39:32Z mjaz $
 * @package		Koowa_Factory
 * @copyright	Copyright (C) 2007 - 2008 Joomlatools. All rights reserved.
 * @license		GNU GPLv2 <http://www.gnu.org/licenses/old-licenses/gpl-2.0.html>
 */

/**
 * KFactory class
 *
 * @author		Johan Janssens <johan@joomlatools.org>
 * @package     Koowa_Factory
 * @static
 */
class KFactory
{
	/**
	 * The object container
	 *
	 * @var	array
	 */
	protected static $_container = null;
	
	/**
	 * The commandchain
	 *
	 * @var	object
	 */
	protected static $_chain = null;
	
	/**
	 * Constructor
	 * 
	 * Prevent creating instances of this class by making the contrucgtor private
	 */
	private function __construct() { }
	
	/**
	 * Get an instance of a class based on a class identifier
	 *
	 * @param mixed  $identifier	The class identifier
	 * @param array  $options 		An optional associative array of configuration settings.
	 *
	 * @throws KFactoryException
	 * @return object
	 */
	public static function get($identifier, array $options = array())
	{
		static $initialized;
	
		if(!isset($initialized)) {
			self::_initialize();
			$initialized = true;
		}
		
		//Check if the object already exists
		if(self::$_container->offsetExists($identifier)) {
			return self::$_container->offsetGet($identifier);
		} 
		
		//Get a handle for the oject 
		$handle = self::$_chain->run($identifier, $options);
		if($handle !== false) {	
			return self::$_container->offsetGet($handle);
		}	
		
		throw new KFactoryException('Cannot create object from identifier : '.$identifier);
	}
	
	/**
	 * Insert the object instance using the identifier
	 *
	 * @param mixed  $identifier 	The class identifier
	 * @param object $object 		The object instance to store
	 */
	public static function set($identifier, $object)
	{
		self::$_container->offsetSet($identifier, $object);
	}
	
	/**
	 * Remove the object instance using the identifier
	 *
	 * @param mixed  $identifier 	The class identifier
	 *
	 * @return boolean Returns TRUE on success or FALSE on failure.
	 */
	public static function del($identifier)
	{
		if(self::$_container->offsetExists($identifier)) {
			self::$_container->offsetUnset($identifier);
			return true;
		}
		
		return false;
	}
	
	/**
	 * Check if the object instance exists based on the identifier
	 *
	 * @param mixed  $identifier 	The class identifier
	 *
	 * @return boolean Returns TRUE on success or FALSE on failure.
	 */
	public static function has($identifier)
	{
		if(self::$_container->offsetExists($identifier)) {
			return true;
		}
		
		return false;
	}
	
	/**
	 * Register a factory adapter
	 * 
	 * @param object 	$adapter	A KFactoryAdapter
	 * @param integer	$priority	The command priority
	 *
	 * @return boolean Returns TRUE on success or FALSE on failure. 
	 */
	public function registerAdapter(KFactoryAdapterInterface $adapter, $priority = 1)
	{
		self::$_chain->enqueue($adapter, $priority);
		return true;
	}
	
	/**
	 * Initialize
	 */	
	protected static function _initialize()
	{	
		//Created the object container
		self::$_container    = new ArrayObject();
	
		//Create the command chain and register the adapters
        self::$_chain = new KFactoryChain();
        self::registerAdapter(new KFactoryAdapterKoowa());
       	self::registerAdapter(new KFactoryAdapterJoomla());
        self::registerAdapter(new KFactoryAdapterComponent());
	}
}
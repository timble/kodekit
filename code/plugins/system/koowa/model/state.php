<?php
/**
 * @version		$Id$
 * @category	Koowa
 * @package		Koowa_Model
 * @copyright	Copyright (C) 2007 - 2009 Johan Janssens and Mathias Verraes. All rights reserved.
 * @license		GNU GPLv2 <http://www.gnu.org/licenses/old-licenses/gpl-2.0.html>
 * @link     	http://www.koowa.org
 */

/**
 * State Model
 *
 * @author		Johan Janssens <johan@koowa.org>
 * @category	Koowa
 * @package     Koowa_Model
 */
class KModelState extends KModelAbstract
{
	/**
	 * Initializes the options for the object
	 *
	 * Called from {@link __construct()} as a first step of object instantiation.
	 *
	 * @param   array   Options
	 * @return  array   Options
	 */
	protected function _initialize(array $options)
	{
		$defaults = array(
            'state'      => array(),
			'identifier' => null
       	);

        return array_merge($defaults, $options);
    }
    
	/**
     * Retrieve state value
     *
     * @param  	string 	The user-specified state name.
     * @return 	string 	The corresponding state value.
     */
    public function __get($name)
    {
    	return $this->_state[$name];
    }

    /**
     * Set state value
     *
     * @param  	string 	The state name.
     * @param  	mixed  	The state value.
     * @return 	void
     */
    public function __set($name, $value)
    {
        $this->_data[$name] = $value;
   }

	/**
     * Test existence of a state 
     *
     * @param  string  The column key.
     * @return boolean
     */
    public function __isset($name)
    {
    	return array_key_exists($name, $this->_state);
    }

    /**
     * Unset a state
     *
     * @param	string  The column key.
     * @return	void
     */
    public function __unset($name)
    {
        unset($this->_state[$name]);
    }
    
	/**
     * Set the object properties
     *
     * @param   string|array|object	The name of the property, an associative array of properties or an object
     * @param   mixed  				The value of the property to set
     * @throws	KObjectException
     * @return  KObject
     */
    public function set( $property, $value = null )
    {
   	 	if(is_object($property)) {
    		$property = (array) $property;
    	}
    	
    	if(is_array($property)) 
        {
        	foreach ($property as $k => $v) {
            	$this->set($k, $v);
        	}
        }
        else $this->_state[$property] = $value;
    	
        return $this;
    }

    /**
     * Get the object properties
     * 
     * If no property name is given then the function will return an associative
     * array of all properties.
     * 
     * If the property does not exist and a  default value is specified this is
     * returned, otherwise the function return NULL.
     *
     * @param   string	The name of the property
     * @param   mixed  	The default value
     * @return  mixed 	The value of the property or an associative array of properties or NULL
     */
    public function get($property = null, $default = null)
    {
        $result = $default;
    	
    	if(is_null($property)) {
        	$result  = $this->_state;
        } 
 		else
        {
    		if(isset($this->_state[$property])) {
            	$result = $this->_state[$property];
        	}
        }
        
        return $result;
    }
}
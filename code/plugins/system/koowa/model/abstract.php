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
 * Abstract Model Class
 *
 * @author		Johan Janssens <johan@koowa.org>
 * @category	Koowa
 * @package     Koowa_Model
 * @uses		KObject
 */
abstract class KModelAbstract extends KObject implements KFactoryIdentifiable
{
	/**
	 * A state object
	 *
	 * @var object
	 */
	protected $_state;

	/**
	 * List total
	 *
	 * @var integer
	 */
	protected $_total;

	/**
	 * Model list data
	 *
	 * @var array
	 */
	protected $_list;

	/**
	 * Model item data
	 *
	 * @var mixed
	 */
	protected $_item;

	/**
	 * The object identifier
	 *
	 * @var object 
	 */
	protected $_identifier = null;

	/**
	 * Constructor
	 *
	 * @param	array An optional associative array of configuration settings.
	 */
	public function __construct(array $options = array())
	{
		// Set the objects identifier
        $this->_identifier = $options['identifier'];
		
		// Initialize the options
		$options  = $this->_initialize($options);

		//Use KObject to store the model state
		$this->_state = new KObject();
		$this->_state->setProperties($options['state']);
	}

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
	 * Get the identifier
	 *
	 * @return 	object A KFactoryIdentifier object
	 * @see 	KFactoryIdentifiable
	 */
	public function getIdentifier()
	{
		return $this->_identifier;
	}

    /**
     * Reset all cached data
     *
     * @return KModelAbstract
     */
    public function reset()
    {
    	unset($this->_list);
    	unset($this->_item);
    	unset($this->_total);
    	
    	//$this->_state = new KObject();

    	return $this;
    }

	/**
	 * Method to set model state variables
	 *
	 * @param	string|array	The name of the property or an associative array of properties
	 * @param	mixed			The value of the property to set
	 * @return	KModelAbstract
	 */
	public function setState( $property, $value = null )
	{
		if(is_array($property)) {
			$this->_state->setProperties($property);	
		} else {
			$this->_state->set($property, $value);
		}
		
		// changing state empties the model's cache because the data is now different
		$this->reset();

		return $this;
	}

	/**
	 * Method to get model state variables
	 *
	 * @param	string	Optional parameter name
	 * @param   mixed	Optional default value
	 * @return	object	The property where specified, the state object where omitted
	 */
	public function getState($property = null, $default = null)
	{
		return $property === null ? $this->_state : $this->_state->get($property, $default);
	}

	/**
	 * Method to get a ite
	 *
	 * @return  object
	 */
	public function getItem()
	{
		return $this->_item;
	}

	/**
	 * Get a list of items
	 *
	 * @return  object
	 */
	public function getList()
	{
		return $this->_list;
	}

	/**
	 * Get the total amount of items
	 *
	 * @return  int
	 */
	public function getTotal()
	{
		return $this->_total;
	}
}
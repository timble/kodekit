<?php
/**
 * @version		$Id$
 * @category	Koowa
 * @package		Koowa_Model
 * @copyright	Copyright (C) 2007 - 2008 Joomlatools. All rights reserved.
 * @license		GNU GPLv2 <http://www.gnu.org/licenses/old-licenses/gpl-2.0.html>
 * @link     	http://www.koowa.org
 */

/**
 * Abstract Model Class
 *
 * @author		Johan Janssens <johan@joomlatools.org>
 * @category	Koowa
 * @package     Koowa_Model
 * @uses		KMixinClass
 * @uses		KInflector
 * @uses		KObject
 * @uses		KFactory
 */
abstract class KModelAbstract extends KObject
{
	/**
	 * Database Connector
	 *
	 * @var object
	 */
	protected $_db;

	/**
	 * A state object
	 *
	 * @var KRegistry object
	 */
	protected $_state;

    /**
     * List total
     *
     * @var integer
     */
    protected $_total;

    /**
     * Pagination object
     *
     * @var object
     */
    protected $_pagination;

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
	 * Constructor
     *
     * @param	array An optional associative array of configuration settings.
	 */
	public function __construct(array $options = array())
	{
        // Initialize the options
        $options  = $this->_initialize($options);

        // Mixin the KClass
        $this->mixin(new KMixinClass($this, 'Model'));

        // Assign the classname with values from the config
        $this->setClassName($options['name']);

		//set the model state
        // TODO move to KRegistry
        $this->_state = new JRegistry();
		$this->_state->merge($options['state']);
		$this->_setDefaultStates();

		//set the model dbo
		$this->_db = $options['dbo'] ? $options['dbo'] : KFactory::get('lib.joomla.database');
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
            'base_path'     => null,
            'dbo'           => null,
            'name'          => array(
                        'prefix'    => 'k',
                        'base'      => 'model',
                        'suffix'    => 'default'
                        ),
            'state'         => array()
        );

        return array_merge($defaults, $options);
    }

	/**
	 * Method to set model state variables
	 *
	 * @param	string	The name of the property
	 * @param	mixed	The value of the property to set
	 * @return	this
	 */
	public function setState( $property, $value = null )
	{
		$this->_state->set($property, $value);
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
	 * Method to get the database connector object
	 *
	 * @return	object KDatabase connector object
	 */
	public function getDBO()
	{
		return $this->_db;
	}

	/**
	 * Method to set the database connector object
	 *
	 * @param	object	$db	A KDatabase based object
	 * @return	void
	 */
	public function setDBO($db)
	{
		$this->_db = $db;
	}

	/**
	 * Method to get a table object, load it if necessary.
	 *
	 * This function overrides the default model behavior and sets the table
	 * prefix based on the model prefix.
	 *
	 * @param	string	$table 			The name of the table. Optional, defaults to the class name.
	 * @param	string	$component		The name of the component. Optional.
	 * @param	string	$application	The name of the application. Optional.
	 * @param	array	$opations		Options array for view. Optional.
	 * @return	object	The table
	 */
	public function getTable($table = '', $component = '', $application = '', array $options = array())
	{
		if (empty($table)) {
			$table = KInflector::tableize($this->getClassName('suffix'));
		}
	
		if ( empty( $component ) ) {
			$component = $this->getClassName('prefix');
		}
		
		if (empty( $application) )  {
			$application = KFactory::get('lib.joomla.application')->getName();
		}

		//Make sure we are returning a DBO object
		if (!array_key_exists('dbo', $options))  {
			$options['dbo'] = $this->getDBO();
		}
		
		return KFactory::get($application.'::com.'.$component.'.table.'.$table, $options);
	}

    /**
     * Method to get a item object which represents a table row
     *
     * @return  object KDatabaseRow
     */
    public function getItem()
    {
        // Get the data if it doesn't already exist
        if (!isset($this->_item)) {
            $this->_item = $this->getTable()->find((int)$this->getState('id'));
        }

        return $this->_item;
    }

    /**
     * Get a list of items which represnts a  table rowset
     *
     * @return  object KDatabaseRowset
     */
    public function getList()
    {
        // Get the data if it doesn't already exist
        if (!isset($this->_list)) 
        {
        	$this->_list = $this->getTable()->fetchAll(
        		$this->_buildQuery(), 
        		$this->getState('offset'), 
        		$this->getState('limit')
        	);
        }

        return $this->_list;
    }

    /**
     * Get the total amount of items
     *
     * @return  int
     */
    public function getTotal()
    {
        // Get the data if it doesn't already exist
        if (!isset($this->_total)) 
        {
            $this->_db->select( $this->_buildCountQuery());
			$this->_total = $this->_db->loadResult();
        }

        return $this->_total;
    }

    /**
     * Get a Pagination object
     *
     * @return  JPagination
     */
    public function getPagination()
    {
        // Get the data if it doesn't already exist
        if (!isset($this->_pagination))
        {
            Koowa::import('lib.joomla.html.pagination');
            $this->_pagination = new JPagination($this->getTotal(), $this->getState('offset'), $this->getState('limit'));
        }

        return $this->_pagination;
    }

    /**
     * Get a list of filters
     *
     * @return  array
     */
    public function getFilters()
    {
        static $filters;

        if (is_null($filters))
        {
            $filters['limit']       = $this->getState('limit');
            $filters['limitstart']  = $this->getState('offset');
            $filters['order']       = $this->getState('order');
            $filters['order_Dir']   = $this->getState('order_Dir');
            $filters['filter']      = $this->getState('filter');
        }

        return $filters;
    }
    
    /**
     * Get the primary key's name
     *
     * @return	string
     */
    protected function _getPrimaryKey() 
    {
    	$name       = $this->getClassName();
        return $name['prefix'] .'_'. KInflector::singularize($name['suffix']) .'_id';
    }

    /**
     * Builds a generic SELECT query
     *
     * @return  string  SELECT query
     */
    protected function _buildQuery()
    {
        $query  = 'SELECT '
                . $this->_buildQueryFields().' '
                . $this->_buildQueryFrom().' '
                . $this->_buildQueryJoins().' '
                . $this->_buildQueryWhere().' '
                . $this->_buildQueryOrder();
                
		return $query;
    }
    
 	/**
     * Builds a generic SELECT COUNT(*) query
     *
     * @return  string  SELECT query
     */
    protected function _buildCountQuery()
    {
        $query  = 'SELECT COUNT(*) '
                . $this->_buildQueryFrom().' '
                . $this->_buildQueryJoins().' '
                . $this->_buildQueryWhere();
                
		return $query;
    }
    
    /**
     * Builds SELECT fields list for the query
     *
     * @return  string  Fields list
     */
    protected function _buildQueryFields()
    {
    	$keyname = $this->_getPrimaryKey();
        return "tbl.*, tbl.`$keyname` AS id";
    }
    
	/**
     * Builds FROM tables list for the query
     *
     * @return  string  FROm tables list
     */
    protected function _buildQueryFrom()
    {
    	$name       = $this->getClassName();
        $tablename  = $name['prefix'] .'_'. KInflector::tableize($name['suffix']);
        return "FROM `#__$tablename` AS tbl";
    }

    /**
     * Builds LEFT JOINS clauses for the query
     *
     * @return  string  LEFT JOIN clauses
     */
    protected function _buildQueryJoins()
    {
        return '';
    }

    /**
     * Builds a WHERE clause for the query
     *
     * @return  string  WHERE clause
     */
    protected function _buildQueryWhere()
    {
        // TODO a generic WHERE clause based on filters?
        return 'WHERE 1';
    }

    /**
     * Builds a generic ORDER BY clasue based on the model's state
     *
     * @return  string  ORDER BY clause or empty
     */
    protected function _buildQueryOrder()
    {
        static $orderby;

        if (!isset($orderby))
        {
            // Assemble the clause pieces
            $order      = $this->getState('order');
            $order_Dir  = strtoupper($this->getState('order_Dir'));

            // Assemble the clause
            $orderby    = $order ? 'ORDER BY '.$order.' '.$order_Dir : '';
        }

        return $orderby;
    }
    
    /**
     * Set default states
     */
    protected function _setDefaultStates()
    {
        $app        = KFactory::get('lib.joomla.application');

        $ns         = $this->getClassName('prefix').'.'.$this->getClassName('suffix');

        // Get the display environment variables
        $limit      = $app->getUserStateFromRequest('global.list.limit', 'limit', $app->getCfg('list_limit'), 'int');
        $offset	 	= $app->getUserStateFromRequest($ns.'limitstart', 'limitstart', 0, 'int');
        $order      = $app->getUserStateFromRequest($ns.'filter_order', 'filter_order', '', 'cmd');
        $order_Dir  = $app->getUserStateFromRequest($ns.'filter_order_Dir', 'filter_order_Dir', 'ASC', 'word');
        $filter     = $app->getUserStateFromRequest($ns.'filter', 'filter', '', 'string');
        $id         = KInput::get('id', 'request', 'raw'); //TODO fix this filter
        
        // Push the environment states into the object
        $this->setState('limit',        $limit);
        $this->setState('offset',   	$offset);
        $this->setState('order',        $order);
        $this->setState('order_Dir',    $order_Dir);
        $this->setState('filter',       $filter);
        $this->setState('id',           $id);
    }

}
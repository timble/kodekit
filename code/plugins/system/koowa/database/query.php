<?php
/**
 * @version		$Id$
 * @category	Koowa
 * @package     Koowa_Database
 * @subpackage  Query
 * @copyright	(C) 2007 - 2008 Joomlatools. All rights reserved.
 * @license		GNU GPLv2 <http://www.gnu.org/licenses/old-licenses/gpl-2.0.html>
 * @link     	http://www.koowa.org
 */

/**
 * Database Select Class for SQL select statement generation
 *
 * @author		Johan Janssens <johan@joomlatools.org>
 * @category	Koowa
 * @package     Koowa_Database
 * @subpackage  Query
 */
class KDatabaseQuery extends KObject
{
	/**
	 * The operation to perform
	 *
	 * @var array
	 */
	public $operation = '';
	
	/**
	 * The columns
	 *
	 * @var array
	 */
	public $columns = array();
	
	/**
	 * The from element
	 *
	 * @var array
	 */
	public $from = array();

	/**
	 * The join element
	 *
	 * @var array
	 */
	public $join = array();

	/**
	 * The where element
	 *
	 * @var array
	 */
	public $where = array();

	/**
	 * The group element
	 *
	 * @var array
	 */
	public $group = array();

	/**
	 * The having element
	 *
	 * @var array
	 */
	public $having = array();

	/**
	 * The order element
	 *
	 * @var string
	 */
	public $order = array();

	/**
	 * The limit element
	 *
	 * @var integer
	 */
	public $limit = null;

	/**
	 * The limit offset element
	 *
	 * @var integer
	 */
	public $offset = null;

	/**
	 * Database connector
	 *
	 * @var		object
	 */
	protected $_db;
	
	/**
	 * Table prefix
	 *
	 * @var		object
	 */
	protected $_prefix = '';
	
	/**
	 * Object constructor
	 *
	 * Can be overloaded/supplemented by the child class
	 *
	 * @param	array An optional associative array of configuration settings.
	 *                Recognized key values include 'dbo' (this list is not
	 * 				  meant to be comprehensive).
	 */
	public function __construct( array $options = array() )
	{
        // Initialize the options
        $options  = $this->_initialize($options);

		//set the model dbo
		$this->_db    = $options['dbo'] ? $options['dbo'] : KFactory::get('lib.joomla.database');
		
		//set the table prefix
		if(isset($options['table_prefix'])) {
			$this->_prefix = $options['table_prefix'];
		}
	}


    /**
     * Initializes the options for the object
     *
     * @param   array   Options
     * @return  array   Options
     */
    protected function _initialize($options)
    {
        $defaults = array(
            'dbo'   => null
        );

        return array_merge($defaults, $options);
    }

	/**
	 * Built a select query
	 *
	 * @param	array|string	A string or an array of field names
	 * @return object KDatabaseQuery
	 */
	public function select( $columns = '*')
	{
		settype($columns, 'array'); //force to an array
		
		//Quote the identifiers
		$columns = $this->_db->quoteName($columns);

		$this->operation = 'SELECT';
		$this->columns   = array_unique( array_merge( $this->columns, $columns ) );
		return $this;
	}
	
	/**
	 * Built a count query
	 *
	 * @return object KDatabaseQuery
	 */
	public function count()
	{
		$this->operation = 'SELECT COUNT(*) ';
		$this->columns    = array();
		return $this;
	}
	
	/**
	 * Make the query distinct
	 *
	 * @return object KDatabaseQuery
	 */
	public function distinct()
	{
		$this->operation = 'SELECT DISTINCT ';
		return $this;
	}

	/**
	 * Built the from clause of the query
	 *
	 * @param	array|string	A string or array of table names
	 * @return object KDatabaseQuery
	 */
	public function from( $tables )
	{
		settype($tables, 'array'); //force to an array
		
		//Prepent the table prefix 
		array_walk($tables, array($this, '_prefix'));
		
		//Quote the identifiers
		$tables = $this->_db->quoteName($tables);
		
		$this->from = array_unique( array_merge( $this->from, $tables ) );
		return $this;
	}
	
	/**
     * Built the join clause of the query
     * 
     * @param 	string 			$type  		The type of join; empty for a plain JOIN, or "LEFT", "INNER", etc.
     * @param 	string 			$table 		The table name to join to.
     * @param 	KDatabaseQuery 	$condition  Join on this condition.
     * @return 	KDatabaseQuery
     */
    public function join($type, $table, KDatabaseQuery $condition)
    {     
		$this->_prefix($table); //add a prefix to the table
    	
		//Quote the identifiers
		$table     = $this->_db->quoteName($table);
	    	
    	$this->join[] = array(
        	'type'  	=> strtoupper($type),
        	'table' 	=> $table,
        	'condition' => $condition,
        );
          
        return $this;
    }
	
	/**
	 * Built the where clause of the query
	 *
	 * Automatically quotes the data values. If constraint is 'IN' the data values will not be quoted.
	 *
	 * @param   string 			The name of the property the constraint applies too
	 * @param	string  		The comparison used for the constraint
	 * @param	string|array	The value compared to the property value using the constraint
	 * @return 	object 	KDatabaseQuery
	 */
	public function where( $property, $constraint, $value )
	{
		// Apply quotes to the property name
		$property = $this->_db->quoteName($property);

		//Apply quotes to the propety value
		if($constraint != 'IN' && !is_numeric($value)) {
			$value = $this->_db->Quote($value);
		}
		
		//Apply quotes to the propety value
		if ( $constraint == 'IN' && is_array($value) )  {
            $value = implode(',', $value);   
        }
		
		//Create the constraint
		$where = $property.' '.$constraint.' '.$value;

		$this->where = array_unique( array_merge( $this->where, array($where) ));
		return $this;
	}

	/**
	 * Built the group clause of the query
	 *
	 * @param	array|string	A string or array of ordering columns
	 * @return object KDatabaseQuery
	 */
	public function group( $columns )
	{
		settype($columns, 'array'); //force to an array
		
		//Quote the identifiers
		$columns = $this->_db->quoteName($columns);

		$this->group = array_unique( array_merge( $this->group, $columns));
		return $this;
	}

	/**
	 * Built the having clause of the query
	 *
	 * @param	array|string	A string or array of ordering columns
	 * @return object KDatabaseQuery
	 */
	public function having( $columns )
	{
		settype($columns, 'array'); //force to an array
		
		//Quote the identifiers
		$columns = $this->_db->quoteName($columns);

		$this->having = array_unique( array_merge( $this->having, $columns ));
		return $this;
	}

	/**
	 * Built the order clause of the query
	 *
	 * @param	array|string  $columns		A string or array of ordering columns
	 * @param	string		  $direction	Either DESC or ASC
	 * @return object KDatabaseQuery
	 */
	public function order( $columns, $direction = 'ASC' )
	{
		settype($columns, 'array'); //force to an array
		
		//Quote the identifiers
		$columns = $this->_db->quoteName($columns);
		
		foreach($columns as $column) 
		{
			$this->order[] = array(
        		'column'  	=> $column,
        		'direction' => $direction
        	);
		}

		return $this;
	}

	/**
	 * Built the limit element of the query
	 *
	 * @param integer $limit 	Number of items to fetch.
	 * @param integer $offset 	Offset to start fetching at.
	 * @return object KDatabaseQuery
	 */
	public function limit( $limit, $offset = null )
	{
		$this->limit  = $limit;
		$this->offset = $offset;
		return $this;
	}

	/**
	 * Render the query to a string
	 *
	 * @return	string	The completed query
	 */
	public function __toString()
	{
		$query = '';
		
		$query .= $this->operation."\n";

		if (!empty($this->columns)) {
			$query .= implode(' , ', $this->columns)."\n";
		}

		if (!empty($this->from)) {
			$query .= ' FROM '.implode(' , ', $this->from)."\n";
		}
		
		if (!empty($this->join))
		{
			$joins = array();
            foreach ($this->join as $join) 
            {
            	$tmp = '';
                
            	if (! empty($join['type'])) {
                    $tmp .= $join['type'] . ' ';
                }
               
                $tmp .= 'JOIN ' . $join['table'];
           		$tmp .= ' ON ' . implode(' AND ', $join['condition']->where);
                
                $joins[] = $tmp;
            }
            
            $query .= implode("\n", $joins) . "\n";
		}

		if (!empty($this->where)) {
			$query .= ' WHERE '.implode(' AND ', $this->where)."\n";
		}

		if (!empty($this->_group)) {
			$query .= ' GROUP BY '.implode(' , ', $this->group)."\n";
		}

		if (!empty($this->_having)) {
			$query .= ' HAVING '.implode(' , ', $this->having)."\n";
		}
		
		if (!empty($this->order) ) 
		{
			$query .= 'ORDER BY ';
			
			$list = array();
            foreach ($this->order as $order) {
            	$list[] = $order['column'].' '.$order['direction'];
            }
            
            $query .= implode(' , ', $list) . "\n";
		}
	
		if (isset($this->limit)) {
			$query .= ' LIMIT '.$this->limit.' , '.$this->offset."\n";
		}
		
		return $query;
	}
	
	/*
	 * Callback for array_walk to prefix elements of array with given 
	 * prefix
	 * 
	 * @param string $data 	The data to be prefixed
	 */
	protected function _prefix(&$data)
	{	
		// Prepend the table modifier
		$data = $this->_prefix.$data;
	}
}
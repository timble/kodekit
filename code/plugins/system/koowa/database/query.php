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
 * @uses        KObject
 * @uses        KFactory
 */
class KDatabaseQuery extends KObject
{
	/**
	 * The operation to perform
	 *
	 * @var array
	 */
	protected $_operation = '';
	
	/**
	 * The columns
	 *
	 * @var array
	 */
	protected $_columns = array();
	
	/**
	 * The from element
	 *
	 * @var array
	 */
	protected $_from = array();

	/**
	 * The join element
	 *
	 * @var array
	 */
	protected $_join = array();

	/**
	 * The where element
	 *
	 * @var array
	 */
	protected $_where = array();

	/**
	 * The group element
	 *
	 * @var array
	 */
	protected $_group = array();

	/**
	 * The having element
	 *
	 * @var array
	 */
	protected $_having = array();

	/**
	 * The order element
	 *
	 * @var string
	 */
	protected $_order = array();

	/**
	 * The limit element
	 *
	 * @var integer
	 */
	protected $_limit = null;

	/**
	 * The limit offset element
	 *
	 * @var integer
	 */
	protected $_offset = null;

	/**
	 * Database connector
	 *
	 * @var		object
	 */
	protected $_db;
	
	/**
	 * Object constructor
	 *
	 * Can be overloaded/supplemented by the child class
	 *
	 * @param	array An optional associative array of configuration settings.
	 *                Recognized key values include 'dbo' (this list is not
	 * 				  meant to be comprehensive).
	 */
	public function __construct( $options = array() )
	{
        // Initialize the options
        $options  = $this->_initialize($options);

		//set the model dbo
		$this->_db = $options['dbo'] ? $options['dbo'] : KFactory::get('lib.joomla.database');
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

		$this->_operation = 'SELECT';
		$this->_columns   = array_unique( array_merge( $this->_columns, $columns ) );
		return $this;
	}
	
	/**
	 * Built a count query
	 *
	 * @return object KDatabaseQuery
	 */
	public function count()
	{
		$this->_operation = 'SELECT COUNT(*) ';
		$this->_columns    = array();
		return $this;
	}
	
	/**
	 * Make the query distinct
	 *
	 * @return object KDatabaseQuery
	 */
	public function distinct()
	{
		$this->_operation = 'SELECT DISTINCT ';
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
		
		$this->_from = array_unique( array_merge( $this->_from, $tables ) );
		return $this;
	}
	
	/**
     * Built the join clause of the query
     * 
     * @param string 		$type  The type of join; empty for a plain JOIN, or "LEFT", "INNER", etc.
     * @param string 		$table The table name to join to.
     * @param string 		$cond  Join on this condition.
     * @param array|string 	$cols  The columns to select from the joined table.
     * @return object KDatabaseQuery
     */
    public function join($type, $table, $condition)
    {     
		settype($columns, 'array'); //force to an array
    	
		$this->_prefix($table); //add a prefix to the table
    	
		//Quote the identifiers
		$table     = $this->_db->quoteName($table);
		$condition = $this->_db->quoteName($condition);
		$columns   = $this->_db->quoteName($columns);
	    	
    	$this->_join[] = array(
        	'type'  	=> $type,
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
		if($constraint != 'IN') {
			$value = $this->_db->Quote($value);
		}
		
		//Apply quotes to the propety value
		if ( $constraint == 'IN' && is_array($value) )  {
            $value = implode(',', $value);   
        }
		
		//Create the constraint
		$where = $property.' '.$constraint.' '.$value;

		$this->_where = array_unique( array_merge( $this->_where, array($where) ));
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

		$this->_group = array_unique( array_merge( $this->_group, $columns));
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

		$this->_having = array_unique( array_merge( $this->_having, $columns ));
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

		$this->_order[$direction] = array_unique( array_merge( $this->_order, $columns ));
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
		$this->_limit  = $limit;
		$this->_offset = $offset;
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
		
		$query .= $this->_operation."\n";

		if (!empty($this->_columns)) {
			$query .= implode(' , ', $this->_columns)."\n";
		}

		if (!empty($this->_from)) {
			$query .= ' FROM '.implode(' , ', $this->_from)."\n";
		}
		
		if (!empty($this->_join))
		{
			 $list = array();
            foreach ($this->_join as $join) 
            {
            	$tmp = '';
                // add the type (LEFT, INNER, etc)
                if (! empty($join['type'])) {
                    $tmp .= $join['type'] . ' ';
                }
                // add the table name and condition
                $tmp .= 'JOIN ' . $join['table'];
                $tmp .= ' ON ' . $join['condition'];
                
                // add to the list
                $list[] = $tmp;
            }
            
            // add the list of all joins
            $query .= implode("\n", $list) . "\n";
		}

		if (!empty($this->_where)) {
			$query .= ' WHERE '.implode(' AND ', $this->_where)."\n";
		}

		if (!empty($this->_group)) {
			$query .= ' GROUP BY '.implode(' , ', $this->_group)."\n";
		}

		if (!empty($this->_having)) {
			$query .= ' HAVING '.implode(' , ', $this->_having)."\n";
		}

		if (!empty($this->_order['DESC'])) {
			$query .= ' ORDER BY '.implode(' , ', $this->_order['DESC']). ' DESC '."\n";
		}
		
		if (!empty($this->_order['ASC'])) {
			$query .= ' ORDER BY '.implode(' , ', $this->_order['ASC']). ' DESC '."\n";
		}

		if (isset($this->_limit)) {
			$query .= ' LIMIT '.$this->_limit.' , '.$this->_offset."\n";
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
		//Prepend the table modifier
		$data = '#__'.$data;
	}
}
<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright	Copyright (C) 2007 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/koowa for the canonical source repository
 */


/**
 * Table Model
 *
 * Provides interaction with a database table
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Koowa\Library\Model
 */
class KModelTable extends KModelAbstract
{
    /**
     * Table object or identifier (APP::com.COMPONENT.table.TABLENAME)
     *
     * @var string|object
     */
    protected $_table = false;

    /**
     * Constructor
     *
     * @param   KConfig $config Configuration options
     */
    public function __construct(KConfig $config)
    {
        parent::__construct($config);

       $this->_table = $config->table;

        // Set the static states
        $this->_state
            ->insert('limit'    , 'int')
            ->insert('offset'   , 'int')
            ->insert('sort'     , 'cmd')
            ->insert('direction', 'word', 'asc')
            ->insert('search'   , 'string')
            // callback state for JSONP, needs to be filtered as cmd to prevent XSS
            ->insert('callback' , 'cmd');

        //Try getting a table object
        if($this->isConnected())
        {
            // Set the dynamic states based on the unique table keys
            foreach($this->getTable()->getUniqueColumns() as $key => $column) {
                $this->_state->insert($key, $column->filter, null, true, $this->getTable()->mapColumns($column->related, true));
            }
        }
    }

    /**
     * Initializes the config for the object
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param   KConfig $config Configuration options
     * @return  void
     */
    protected function _initialize(KConfig $config)
    {
        $config->append(array(
            'table' => $this->getIdentifier()->name,
        ));

        parent::_initialize($config);
    }

    /**
     * Set the model state properties
     *
     * This function overloads the KDatabaseTableAbstract::set() function and only acts on state properties.
     *
     * @param   string|array|object $property The name of the property, an associative array or an object
     * @param   mixed               $value    The value of the property
     * @return  KModelTable
     */
    public function set( $property, $value = null )
    {
        parent::set($property, $value);

        // If limit has been changed, adjust offset accordingly
        if($limit = $this->_state->limit) {
             $this->_state->offset = $limit != 0 ? (floor($this->_state->offset / $limit) * $limit) : 0;
        }

        return $this;
    }

    /**
     * Method to get a table object
     *
     * Function catches RuntimeException that are thrown for tables that don't exist. If no table object can be created
     * the function will return FALSE.
     *
     * @return KDatabaseTableAbstractInterface
     */
    public function getTable()
    {
        if($this->_table !== false)
        {
            if(!($this->_table instanceof KDatabaseTableAbstract))
		    {
		        //Make sure we have a table identifier
		        if(!($this->_table instanceof KServiceIdentifier)) {
		            $this->setTable($this->_table);
			    }

		        try {
		            $this->_table = $this->getService($this->_table);
                } catch (RuntimeException $e) {
                    $this->_table = false;
                }
            }
        }

        return $this->_table;
    }

    /**
     * Method to set a table object attached to the model
     *
     * @param	mixed	$table An object that implements KObjectServiceable, KServiceIdentifier object
	 * 					       or valid identifier string
     * @throws  UnexpectedValueException    If the identifier is not a table identifier
     * @return  KModelTable
     */
    public function setTable($table)
	{
		if(!($table instanceof KDatabaseTableAbstract))
		{
			if(is_string($table) && strpos($table, '.') === false )
		    {
		        $identifier         = clone $this->getIdentifier();
		        $identifier->path   = array('database', 'table');
		        $identifier->name   = KInflector::tableize($table);
		    }
		    else  $identifier = $this->getIdentifier($table);

			if($identifier->path[1] != 'table') {
				throw new UnexpectedValueException('Identifier: '.$identifier.' is not a table identifier');
			}

			$table = $identifier;
		}

		$this->_table = $table;

		return $this;
	}

	/**
	 * Test the connected status of the row.
	 *
	 * @return	boolean	Returns TRUE if we have a reference to a live KDatabaseTableAbstract object.
	 */
    public function isConnected()
	{
	    return (bool) $this->getTable();
	}

    /**
     * Method to get a item object which represents a table row
     *
     * If the model state is unique a row is fetched from the database based on the state.
     * If not, an empty row is be returned instead.
     *
     * @return KDatabaseRowInterface
     */
    public function getItem()
    {
        if (!isset($this->_item))
        {
            if($this->isConnected())
            {
                $query  = null;

                if($this->_state->isUnique())
                {
                	$query = $this->getService('koowa:database.query.select');

                	$this->_buildQueryColumns($query);
                	$this->_buildQueryTable($query);
                	$this->_buildQueryJoins($query);
                	$this->_buildQueryWhere($query);
                	$this->_buildQueryGroup($query);
                	$this->_buildQueryHaving($query);
                }

                $this->_item = $this->getTable()->select($query, KDatabase::FETCH_ROW);
            }
        }

        return $this->_item;
    }

    /**
     * Get a list of items which represnts a  table rowset
     *
     * @return KDatabaseRowsetInterface
     */
    public function getList()
    {
        // Get the data if it doesn't already exist
        if (!isset($this->_list))
        {
            if($this->isConnected())
            {
                $query  = null;

                if(!$this->_state->isEmpty())
                {
                	$query = $this->getService('koowa:database.query.select');

                	$this->_buildQueryColumns($query);
                	$this->_buildQueryTable($query);
                	$this->_buildQueryJoins($query);
                	$this->_buildQueryWhere($query);
                	$this->_buildQueryGroup($query);
                	$this->_buildQueryHaving($query);
                	$this->_buildQueryOrder($query);
                	$this->_buildQueryLimit($query);
                }

                $this->_list = $this->getTable()->select($query, KDatabase::FETCH_ROWSET);
            }
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
            if($this->isConnected())
            {
	            $query = $this->getService('koowa:database.query.select');
	            $query->columns('COUNT(*)');

	            $this->_buildQueryTable($query);
	            $this->_buildQueryJoins($query);
	            $this->_buildQueryWhere($query);

                $total = $this->getTable()->count($query);
                $this->_total = $total;
            }
        }

        return $this->_total;
    }

    /**
     * Builds SELECT columns list for the query
     */
    protected function _buildQueryColumns(KDatabaseQueryInterface $query)
    {
        $query->columns('tbl.*');
    }
    
    /**
     * Builds FROM tables list for the query
     */
    protected function _buildQueryTable(KDatabaseQueryInterface $query)
    {
        $name = $this->getTable()->getName();
        $query->table(array('tbl' => $name));
    }

    /**
     * Builds LEFT JOINS clauses for the query
     */
    protected function _buildQueryJoins(KDatabaseQueryInterface $query)
    {

    }

    /**
     * Builds a WHERE clause for the query
     */
    protected function _buildQueryWhere(KDatabaseQueryInterface $query)
    {
        //Get only the unique states
        $states = $this->_state->getData(true);

        if(!empty($states))
        {
            $states = $this->getTable()->mapColumns($states);
            foreach($states as $key => $value)
            {
                if(isset($value)) 
                {
                    $query->where('tbl.'.$key.' '.(is_array($value) ? 'IN' : '=').' :'.$key)
                           ->bind(array($key => $value));
                }
            }
        }
    }

    /**
     * Builds a GROUP BY clause for the query
     */
    protected function _buildQueryGroup(KDatabaseQueryInterface $query)
    {

    }

    /**
     * Builds a HAVING clause for the query
     */
    protected function _buildQueryHaving(KDatabaseQueryInterface $query)
    {

    }

    /**
     * Builds a generic ORDER BY clause based on the model's state
     */
    protected function _buildQueryOrder(KDatabaseQueryInterface $query)
    {
        $sort       = $this->_state->sort;
        $direction  = strtoupper($this->_state->direction);

        if($sort) {
            $query->order($this->getTable()->mapColumns($sort), $direction);
        }

        if(array_key_exists('ordering', $this->getTable()->getColumns())) {
            $query->order('tbl.ordering', 'ASC');
        }
    }

    /**
     * Builds LIMIT clause for the query
     */
    protected function _buildQueryLimit(KDatabaseQueryInterface $query)
    {
        $limit = $this->_state->limit;

        if($limit)
        {
            $offset = $this->_state->offset;
            $total  = $this->getTotal();

            //If the offset is higher than the total recalculate the offset
            if($offset !== 0 && $total !== 0)
            {
                if($offset >= $total)
                {
                    $offset = floor(($total-1) / $limit) * $limit;
                    $this->_state->offset = $offset;
                }
             }

             $query->limit($limit, $offset);
        }
    }
}
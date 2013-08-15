<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright	Copyright (C) 2007 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/koowa for the canonical source repository
 */

/**
 * Table Database Rowset
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Koowa\Library\Database
 */
class KDatabaseRowsetTable extends KDatabaseRowsetAbstract
{
	/**
	 * Table object or identifier (com://APP/COMPONENT.table.NAME)
	 *
	 * @var	string|object
	 */
	protected $_table = false;

	/**
	 * Constructor
	 *
	 * @param   KConfig $config Configuration options
	 */
	public function __construct(KConfig $config = null)
	{
		parent::__construct($config);

		$this->_table = $config->table;

		// Reset the rowset
        $this->reset();

        // Insert the data, if exists
        if(!empty($config->data)) {
	        $this->addData($config->data->toArray(), $config->new);
        }
	}

	/**
	 * Initializes the options for the object
	 *
	 * Called from {@link __construct()} as a first step of object instantiation.
	 *
	 * @param   KConfig $config Configuration options
	 * @return void
	 */
	protected function _initialize(KConfig $config)
	{
		$config->append(array(
			'table'	=> $this->getIdentifier()->name
		));

		parent::_initialize($config);
	}

	/**
     * Method to get a table object
     *
     * Function catches RuntimeException that are thrown for tables the don't exist. If no table object can be created
     * the function will return FALSE.
     *
     * @return KDatabaseTableAbstract
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
	 * Method to set a table object attached to the rowset
	 *
	 * @param	mixed	$table An object that implements KObjectInterface, KServiceIdentifier object or valid
     *                         identifier string
	 * @throws	UnexpectedValueException	If the identifier is not a table identifier
	 * @return	KDatabaseRowsetAbstract
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
	 * Get an empty row
	 *
	 * @param	array $options An optional associative array of configuration settings.
	 * @return	object	A KDatabaseRow object.
	 */
	public function getRow(array $options = array())
	{
		$result = null;

	    if($this->isConnected()) {
		    $result = $this->getTable()->getRow($options);
		}

	    return $result;
	}

	/**
	 * Forward the call to each row
	 *
	 * This functions overloads KDatabaseRowsetAbstract::__call and implements
	 * a just in time mixin strategy. Available table behaviors are only mixed
	 * when needed.
	 *
	 * @param  string 	$method     The function name
	 * @param  array  	$arguments  The function arguments
	 * @throws BadMethodCallException 	If method could not be found
	 * @return mixed The result of the function
	 */
	public function __call($method, $arguments)
	{
	    // If the method hasn't been mixed yet, load all the behaviors.
		if($this->isConnected() && !isset($this->_mixed_methods[$method]))
		{
			foreach($this->getTable()->getBehaviors() as $behavior) {
				$this->mixin($behavior);
			}
		}

		return parent::__call($method, $arguments);
	}
}

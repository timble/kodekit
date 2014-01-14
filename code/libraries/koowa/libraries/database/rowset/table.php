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
	 * @param   KObjectConfig $config Configuration options
	 */
	public function __construct(KObjectConfig $config = null)
	{
        //Bypass DatabaseRowsetAbstract constructor to prevent data from being added twice
        KObjectSet::__construct($config);

        //Set the row cloning
        $this->_row_cloning = $config->row_cloning;

        //Set the table identifier
        $this->_table = $config->table;

        // Set the table identifier
        if (isset($config->identity_column)) {
            $this->_identity_column = $config->identity_column;
        }

        // Reset the rowset
        $this->reset();

        // Insert the data, if exists
        if (!empty($config->data)) {
            $this->addRow($config->data->toArray(), $config->status);
        }

        //Set the status message
        if (!empty($config->status_message)) {
            $this->setStatusMessage($config->status_message);
        }
	}

	/**
	 * Initializes the options for the object
	 *
	 * Called from {@link __construct()} as a first step of object instantiation.
	 *
	 * @param   KObjectConfig $config Configuration options
	 * @return void
	 */
	protected function _initialize(KObjectConfig $config)
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
            if(!($this->_table instanceof KDatabaseTableInterface))
		    {
		        //Make sure we have a table identifier
		        if(!($this->_table instanceof KObjectIdentifier)) {
		            $this->setTable($this->_table);
			    }

		        try {
		            $this->_table = $this->getObject($this->_table);
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
	 * @param	mixed	$table An object that implements KObjectInterface, KObjectIdentifier object or valid
     *                         identifier string
	 * @throws	UnexpectedValueException	If the identifier is not a table identifier
	 * @return	KDatabaseRowsetAbstract
	 */
	public function setTable($table)
	{
		if(!($table instanceof KDatabaseTableInterface))
		{
			if(is_string($table) && strpos($table, '.') === false )
		    {
		        $identifier         = $this->getIdentifier()->toArray();
		        $identifier['path'] = array('database', 'table');
		        $identifier['name'] = KStringInflector::tableize($table);

                $identifier = $this->getIdentifier($identifier);
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
     * Add rows to the rowset
     *
     * @param  array  $data  An associative array of row data to be inserted.
     * @param  bool   $new   If TRUE, mark the row(s) as new (i.e. not in the database yet). Default TRUE
     * @return $this
     * @see __construct
     */
    public function addRow(array $data, $new = true)
    {
        if ($this->isConnected()) {
            parent::addRow($data, $new);
        }

        return $this;
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
     * Search the mixin method map and call the method or forward the call to each row
     *
     * This function implements a just in time mixin strategy. Available table behaviors are only mixed when needed.
     * Lazy mixing is triggered by calling DatabaseRowTable::is[Behaviorable]();
     *
     * @param  string     $method    The function name
     * @param  array      $arguments The function arguments
     * @return mixed The result of the function
     */
    public function __call($method, $arguments)
    {
        if ($this->isConnected() && !isset($this->_mixed_methods[$method]))
        {
            $parts = KStringInflector::explode($method);

            //Check if a behavior is mixed
            if ($parts[0] == 'is' && isset($parts[1]))
            {
                //Lazy mix behaviors
                $behavior = strtolower($parts[1]);

                if ($this->getTable()->hasBehavior($behavior)) {
                    $this->mixin($this->getTable()->getBehavior($behavior));
                } else {
                    return false;
                }
            }
        }

        return parent::__call($method, $arguments);
    }
}

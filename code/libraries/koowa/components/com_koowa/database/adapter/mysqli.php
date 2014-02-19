<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright	Copyright (C) 2007 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/koowa for the canonical source repository
 */


/**
 * MySQLi Database Adapter
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Koowa\Component\Koowa
 */
class ComKoowaDatabaseAdapterMysqli extends KDatabaseAdapterMysqli implements KObjectMultiton
{
    /**
	 * The cache object
	 *
	 * @var	JCache
	 */
    protected $_cache;

	/**
	 * Constructor
	 *
	 * Prevent creating instances of this class by making the constructor private
	 *
	 * @param   KObjectConfig $config Configuration options
	 */
	public function __construct(KObjectConfig $config)
	{
		parent::__construct($config);

        if(JFactory::getApplication()->getCfg('caching')) {
            $this->_cache = JFactory::getCache('com_koowa.tables', 'output');
        }
	}

    /**
     * Initializes the options for the object
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param   KObjectConfig $config Configuration options.
     * @return  void
     */
    protected function _initialize(KObjectConfig $config)
    {
        $db = JFactory::getDBO();

		$resource = method_exists($db, 'getConnection') ? $db->getConnection() : $db->_resource;
		$prefix   = method_exists($db, 'getPrefix')     ? $db->getPrefix()     : $db->_table_prefix;

        $config->append(array(
    		'connection'   => $resource,
            'table_prefix' => $prefix,
        ));

        parent::_initialize($config);
    }

	/**
	 * Retrieves the table schema information about the given table
	 *
	 * This function try to get the table schema from the cache. If it cannot be found the table schema will be
     * retrieved from the database and stored in the cache.
	 *
	 * @param 	string 	$table A table name or a list of table names
	 * @return	KDatabaseSchemaTable
	 */
	public function getTableSchema($table)
	{
	    if(!isset($this->_table_schema[$table]) && isset($this->_cache))
		{
		    $database = $this->getDatabase();

		    $identifier = md5($database.$table);

	        if (!$schema = $this->_cache->get($identifier))
	        {
	            $schema = parent::getTableSchema($table);

	            //Store the object in the cache
		   	    $this->_cache->store(serialize($schema), $identifier);
	        }
	        else $schema = unserialize($schema);

		    $this->_table_schema[$table] = $schema;
	    }

	    return parent::getTableSchema($table);
	}
}

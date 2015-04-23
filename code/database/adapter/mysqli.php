<?php
/**
 * Nooku Framework - http://nooku.org/framework
 *
 * @copyright   Copyright (C) 2007 - 2014 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/nooku/nooku-framework for the canonical source repository
 */

/**
 * Mysqli Database Adapter
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Koowa\Library\Database\Adapter
 */
class KDatabaseAdapterMysqli extends KDatabaseAdapterAbstract
{
    /**
     * Quote for query identifiers
     *
     * @var string
     */
    protected $_identifier_quote = '`';

    /**
     * The database name of the active connection
     *
     * @var string
     */
    protected $_database;

    /**
     * Map of native MySQL types to generic types used when reading table column information.
     *
     * @var array
     */
    protected $_type_map = array(

        // numeric
        'int'               => 'int',
        'integer'           => 'int',
        'bigint'            => 'int',
        'mediumint'         => 'int',
        'smallint'          => 'int',
        'tinyint'           => 'int',
        'numeric'           => 'numeric',
        'dec'               => 'numeric',
        'decimal'           => 'numeric',
        'float'             => 'float'  ,
        'double'            => 'float'  ,
        'real'              => 'float'  ,

        // boolean
        'bool'              => 'boolean',
        'boolean'           => 'boolean',

        // date & time
        'date'              => 'date'     ,
        'time'              => 'time'     ,
        'datetime'          => 'timestamp',
        'timestamp'         => 'int'  ,
        'year'              => 'int'  ,

        // string
        'national char'     => 'string',
        'nchar'             => 'string',
        'char'              => 'string',
        'binary'            => 'string',
        'national varchar'  => 'string',
        'nvarchar'          => 'string',
        'varchar'           => 'string',
        'varbinary'         => 'string',
        'text'              => 'string',
        'mediumtext'        => 'string',
        'tinytext'          => 'string',
        'longtext'          => 'string',

        // blob
        'blob'              => 'raw',
        'tinyblob'          => 'raw',
        'mediumblob'        => 'raw',
        'longblob'          => 'raw',

        //other
        'set'               => 'string',
        'enum'              => 'string',
    );

    /**
     * Initializes the options for the object
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param   KObjectConfig $config  An optional KObjectConfig object with configuration options.
     * @return  void
     */
    protected function _initialize(KObjectConfig $config)
    {
        $config->append(array(
            'database'  => '',
            'host'      => ini_get('mysqli.default_host'),
            'username'  => ini_get('mysqli.default_user'),
            'password'  => ini_get('mysqli.default_pw'),
            'port'      => ini_get('mysqli.default_port'),
            'socket'    => ini_get('mysqli.default_socket')
        ));

        parent::_initialize($config);
    }

    /**
     * Connect to the db
     *
     * @throws RuntimeException
     * @return KDatabaseAdapterMysqli
     */
     public function connect()
     {
        $oldErrorReporting = error_reporting(0);

        $mysqli = new mysqli(
            $this->getConfig()->host,
            $this->getConfig()->username,
            $this->getConfig()->password,
            $this->getConfig()->database,
            $this->getConfig()->port,
            $this->getConfig()->socket
        );

        error_reporting($oldErrorReporting);

        if (mysqli_connect_errno()) {
            throw new RuntimeException('Connect failed: (' . mysqli_connect_errno() . ') ' . mysqli_connect_error(), mysqli_connect_errno());
        }

        // If supported, request real datatypes from MySQL instead of returning everything as a string.
        if (defined('MYSQLI_OPT_INT_AND_FLOAT_NATIVE')) {
            $mysqli->options(MYSQLI_OPT_INT_AND_FLOAT_NATIVE, true);
        }

        $this->_connection = $mysqli;
        $this->_connected  = true;

        return $this;
    }

    /**
     * Disconnect from db
     *
     * @return KDatabaseAdapterMysqli
     */
    public function disconnect()
    {
        if ($this->isConnected())
        {
            $this->_connection->close();
            $this->_connection = null;
            $this->_connected  = false;
        }

        return $this;
    }

    /**
     * Check if the connection is active
     *
     * @return boolean
     */
    public function isConnected()
    {
        return ($this->_connection instanceof MySQLi) && @$this->_connection->ping();
    }

    /**
     * Set the connection
     *
     * @param 	resource 	$resource The connection resource
     * @return  KDatabaseAdapterAbstract
     * @throws  InvalidArgumentException If the resource is not an MySQLi instance
     */
    public function setConnection($resource)
    {
        if(!($resource instanceof MySQLi)) {
            throw new InvalidArgumentException('Not a MySQLi connection');
        }

        $this->_connection = $resource;
        return $this;
    }

    /**
     * Get the database name
     *
     * @return string	The database name
     */
    public function getDatabase()
    {
        if(!isset($this->_database))
        {
            $query = $this->getObject('lib:database.query.select')
                ->columns('DATABASE()');

            $this->_database = $this->select($query, KDatabase::FETCH_FIELD);
        }
        return $this->_database;
    }

    /**
     * Set the database name
     *
     * @param 	string 	$database The database name
     * @return  KDatabaseAdapterAbstract
     *
     * @throws RuntimeException
     */
    public function setDatabase($database)
    {
        if(!$this->_connection->select_db($database)) {
            throw new RuntimeException('Could not connect with database : '.$database);
        }

        $this->_database = $database;
        return $this;
    }

    /**
     * Retrieves the table schema information about the given table
     *
     * @param 	string 	$table A table name or a list of table names
     * @return	KDatabaseSchemaTable
     */
    public function getTableSchema($table)
    {
        if(!isset($this->_table_schema[$table]))
        {
            $this->_table_schema[$table] = $this->_fetchTableInfo($table);

            $this->_table_schema[$table]->indexes = $this->_fetchTableIndexes($table);
            $this->_table_schema[$table]->columns = $this->_fetchTableColumns($table);
        }

        return $this->_table_schema[$table];
    }

    /**
     * Locks a table
     *
     * @param   string $table  The name of the table.
     * @return  boolean  TRUE on success, FALSE otherwise.
     */
    public function lockTable($table)
    {
        $query = 'LOCK TABLES '.$this->quoteIdentifier($this->getTableNeedle().$table).' WRITE';

        // Create command chain context.
        $context = $this->getContext();
        $context->table = $table;
        $context->query = $query;

        if($this->invokeCommand('before.lock', $context) !== false)
        {
            $context->result = $this->execute($context->query, KDatabase::RESULT_USE);
            $this->invokeCommand('after.lock', $context);
        }

        return $context->result;
    }

    /**
     * Unlocks tables
     *
     * @return  boolean  TRUE on success, FALSE otherwise.
     */
    public function unlockTable()
    {
        $query = 'UNLOCK TABLES';

        // Create command chain context.
        $context = $this->getContext();
        $context->table = null;
        $context->query = $query;

        if($this->invokeCommand('before.unlock', $context) !== false)
        {
            $context->result = $this->execute($context->query, KDatabase::RESULT_USE);
            $this->invokeCommand('after.unlock', $context);
        }

        return $context->result;
    }

    /**
     * Fetch the first field of the first row
     *
     * @param	mysqli_result  	$result The result object. A result set identifier returned by the select() function
     * @param   integer         $key    The index to use
     * @return  mixed           The value returned in the query or null if the query failed.
     */
    protected function _fetchField($result, $key = 0)
    {
        $return = null;
        if($row = $result->fetch_row( )) {
            $return = $row[(int)$key];
        }

        $result->free();

        return $return;
    }

    /**
     * Fetch an array of single field results
     *
     *
     * @param   mysqli_result  	$result The result object. A result set identifier returned by the select() function
     * @param   integer         $key    The index to use
     * @return  array           A sequential array of returned rows.
     */
    protected function _fetchFieldList($result, $key = 0)
    {
        $array = array();

        while ($row = $result->fetch_row( )) {
            $array[] = $row[(int)$key];
        }

        $result->free();

        return $array;
    }

    /**
     * Fetch the first row of a result set as an associative array
     *
     * @param   mysqli_result   $result The result object. A result set identifier returned by the select() function
     * @return array
     */
    protected function _fetchArray($result)
    {
        $array = $result->fetch_assoc( );
        $result->free();

        return $array;
    }

    /**
     * Fetch all result rows of a result set as an array of associative arrays
     *
     * If <var>key</var> is not empty then the returned array is indexed by the value of the database key.
     * Returns <var>null</var> if the query fails.
     *
     * @param   mysqli_result   $result The result object. A result set identifier returned by the select() function
     * @param   string          $key    The column name of the index to use
     * @return  array   If key is empty as sequential list of returned records.
     */
    protected function _fetchArrayList($result, $key = '')
    {
        $array = array();
        while ($row = $result->fetch_assoc( ))
        {
            if ($key) {
                $array[$row[$key]] = $row;
            } else {
                $array[] = $row;
            }
        }

        $result->free();

        return $array;
    }

    /**
     * Fetch the first row of a result set as an object
     *
     * @param   mysqli_result  $result The result object. A result set identifier returned by the select() function
     * @return  object
     */
    protected function _fetchObject($result)
    {
        $object = $result->fetch_object( );
        $result->free();

        return $object;
    }

    /**
     * Fetch all rows of a result set as an array of objects
     *
     * If <var>key</var> is not empty then the returned array is indexed by the value of the database key.
     * Returns <var>null</var> if the query fails.
     *
     * @param   mysqli_result  $result The result object. A result set identifier returned by the select() function
     * @param   string         $key    The column name of the index to use
     * @return  array   If <var>key</var> is empty as sequential array of returned rows.
     */
    protected function _fetchObjectList($result, $key='')
    {
        $array = array();
        while ($row = $result->fetch_object( ))
        {
            if ($key) {
                $array[$row->$key] = $row;
            } else {
                $array[] = $row;
            }
        }

        $result->free();

        return $array;
    }

    /**
     * Safely quotes a value for an SQL statement.
     *
     * @param   mixed $value The value to quote
     * @return string An SQL-safe quoted value
     */
    protected function _quoteValue($value)
    {
        $value =  '\''.mysqli_real_escape_string( $this->_connection, $value ).'\'';
        return $value;
    }

    /**
     * Retrieves the table schema information about the given tables
     *
     * @param   string $table  A table name.
     * @return  KDatabaseSchemaTable or null if the table doesn't exist.
     */
    protected function _fetchTableInfo($table)
    {
        $return = null;
        $query  = $this->getObject('lib:database.query.show')
            ->show('TABLE STATUS')
            ->like(':like')
            ->bind(array('like' => $table));

        if($info = $this->select($query, KDatabase::FETCH_OBJECT)) {
            $return = $this->_parseTableInfo($info);
        }

        return $return;
    }

    /**
     * Retrieves the column schema information about the given table
     *
     * @param   string  $table A table name
     * @return  array   An array of columns
     */
    protected function _fetchTableColumns($table)
    {
        $return = array();
        $query  = $this->getObject('lib:database.query.show')
            ->show('FULL COLUMNS')
            ->from($table);

        if($columns = $this->select($query, KDatabase::FETCH_OBJECT_LIST))
        {
            foreach($columns as $column)
            {
                // Set the table name in the raw info (MySQL doesn't add this).
                $column->Table = $table;

                $column = $this->_parseColumnInfo($column, $table);
                $return[$column->name] = $column;
            }
        }

        return $return;
    }

    /**
     * Retrieves the index information about the given table
     *
     * @param   string  $table A table name
     * @return  array   An associative array of indexes by index name
     */
    protected function _fetchTableIndexes($table)
    {
        $return = array();
        $query  = $this->getObject('lib:database.query.show')
            ->show('INDEX')
            ->from($table);

        if($indexes = $this->select($query, KDatabase::FETCH_OBJECT_LIST))
        {
            foreach($indexes as $index) {
                $return[$index->Key_name][$index->Seq_in_index] = $index;
            }
        }

        return $return;
    }

    /**
     * Parses the raw table schema information
     *
     * @param   object  $info  The raw table schema information.
     * @return  KDatabaseSchemaTable
     */
    protected function _parseTableInfo($info)
    {
        $table              = new KDatabaseSchemaTable();
        $table->name        = $info->Name;
        $table->engine      = $info->Engine;
        $table->type        = $info->Comment == 'VIEW' ? 'VIEW' : 'BASE';
        $table->length      = $info->Data_length;
        $table->autoinc     = $info->Auto_increment;
        $table->collation   = $info->Collation;
        $table->behaviors   = array();
        $table->description = $info->Comment != 'VIEW' ? $info->Comment : '';

        return $table;
    }

    /**
     * Parse the raw column schema information
     *
     * @param   object  $info The raw column schema information
     * @return  KDatabaseSchemaColumn
     */
    protected function _parseColumnInfo($info)
    {
        list($type, $length, $scope) = $this->_parseColumnType($info->Type);

        $column           = new KDatabaseSchemaColumn();
        $column->name     = $info->Field;
        $column->type     = $type;
        $column->length   = $length ? $length : null;
        $column->scope    = $scope ? (int) $scope : null;
        $column->default  = $info->Default;
        $column->required = $info->Null != 'YES';
        $column->primary  = $info->Key == 'PRI';
        $column->unique   = ($info->Key == 'UNI' || $info->Key == 'PRI');
        $column->autoinc  = strpos($info->Extra, 'auto_increment') !== false;
        $column->filter   = $this->_type_map[$type];

        // Don't keep "size" for integers.
        if(substr($type, -3) == 'int') {
            $column->length = null;
        }

        // Get the related fields if the column is primary key or part of a unique multi column index.
        if($indexes = $this->_table_schema[$info->Table]->indexes)
        {
            foreach($indexes as $index)
            {
                // We only deal with composite-unique indexes.
                if(count($index) > 1 && !$index[1]->Non_unique)
                {
                    $fields = array();
                    foreach($index as $field) {
                        $fields[$field->Column_name] = $field->Column_name;
                    }

                    if(array_key_exists($column->name, $fields))
                    {
                        unset($fields[$column->name]);
                        $column->related = array_values($fields);
                        $column->unique = true;
                        break;
                    }
                }
            }
        }

        return $column;
    }

    /**
     * Given a raw column specification, parse into datatype, length, and decimal scope.
     *
     * @param string $spec The column specification; for example, "VARCHAR(255)" or "NUMERIC(10,2)" or "float(6,2)
     *                     UNSIGNED" or ENUM('yes','no','maybe')
     * @return array A sequential array of the column type, size, and scope.
     */
    protected function _parseColumnType($spec)
    {
        $spec    = strtolower($spec);
        $type    = null;
        $length  = null;
        $scope   = null;

        // find the type first
        $type = strtok($spec, '( ');

        // find the parens, if any
        if (false !== ($pos = strpos($spec, '(')))
        {
            // there were parens, so there's at least a length
            // remove parens to get the size.
            $length = trim(substr(strtok($spec, ' '), $pos), '()');

            if($type != 'enum' && $type != 'set')
            {
                // A comma in the size indicates a scope.
                $pos = strpos($length, ',');
                if ($pos !== false)
                {
                    $scope  = substr($length, $pos + 1);
                    $length = substr($length, 0, $pos);
                }

            }
            else $length = explode(',', str_replace("'", "", $length));
        }

        return array($type, $length, $scope);
    }
}

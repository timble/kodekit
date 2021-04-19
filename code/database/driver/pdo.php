<?php
/**
 * Kodekit - http://timble.net/kodekit
 *
 * @copyright   Copyright (C) 2007 - 2016 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license     MPL v2.0 <https://www.mozilla.org/en-US/MPL/2.0>
 * @link        https://github.com/timble/kodekit for the canonical source repository
 */

namespace Kodekit\Library;

/**
 * PDO Database Driver
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Kodekit\Library\Database\Driver
 */
class DatabaseDriverPdo extends DatabaseDriverAbstract
{

    /**
     * The database connection resource
     *
     * @var \PDO
     */
    protected $_connection = null;

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

        //json
        'json'              => 'json',
    );

    public function __construct(ObjectConfig $config = null)
    {
        parent::__construct($config);

        if ($config->database) {
            $this->setDatabase($config->database);
        }
    }

    /**
     * Initializes the options for the object
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param   ObjectConfig $config  An optional KObjectConfig object with configuration options.
     * @return  void
     */
    protected function _initialize(ObjectConfig $config)
    {
        $config->append(array(
            'charset'   => 'utf8mb4',
            'table_prefix' => '',
            'database'  => '',
            'host'      => '',
            'username'  => '',
            'password'  => '',
            'port'      => null,
            'socket'    => '',
            'sqlite_version' => 3,
            'connection_string' => null,
            'driver'    => 'sqlite',
            'driver_options' => [
                \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
            ]
        ));

        parent::_initialize($config);
    }

    /**
     * Connect to the db
     *
     * @throws \RuntimeException
     * @return $this
     */
    public function connect()
    {
        if (!\defined('\\PDO::ATTR_DRIVER_NAME')) {
            throw new \RuntimeException('PDO driver not available');
        }

        $config = $this->getConfig();

        $connectionString = $config->connection_string;

        if (!$connectionString) {
            switch ($config->driver) {
                case 'mysql':
                    $name = 'mysql';
                    $map = [
                        'dbname' => $config->database,
                    ];

                    if ($this->getCharset()) {
                        $map['charset'] = $this->getCharset();
                    }

                    if ($config->socket) {
                        $map['unix_socket'] = $config->socket;
                    } else {
                        $map['host'] = $config->host;
                    }

                    if ($config->port) {
                        $map['port'] = $config->port;
                    }

                    $connectionString = sprintf('%s:%s', $name, implode(';', array_map(function($key, $value) {
                        return sprintf('%s=%s', $key, $value);
                    }, array_keys($map), $map)));

                    break;

                case 'sqlite':
                    $name = $config->sqlite_version == 2 ? 'sqlite2' : 'sqlite';
                    $connectionString = sprintf('%s:%s', $name, $config->database);

                    break;
                default:
                    throw new \RuntimeException('Unknown driver');
            }
        }

        try {
            $this->setConnection(new \PDO(
                $connectionString,
                $config->username,
                $config->password,
                ObjectConfig::unbox($config->driver_options)
            ));
            $this->_connected = true;
        } catch (\PDOException $exception) {
            throw new \RuntimeException('Connect failed: (' . $exception->getCode() . ') ' . $exception->getMessage(), $exception->getCode(), $exception);
        }

        return $this;
    }

    /**
     * Disconnect from db
     *
     * @return $this
     */
    public function disconnect()
    {
        if ($this->isConnected())
        {
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
        try {
            return ($this->getConnection() instanceof \PDO) && ((bool) $this->getConnection()->query('SELECT 1+1'));
        } catch (\PDOException $e) {
            return false;
        }
    }

    /**
     * Set the connection
     *
     * @param 	resource|\PDO 	$resource The connection resource
     * @return  DatabaseDriverAbstract
     * @throws  \InvalidArgumentException If the resource is not an MySQLi instance
     */
    public function setConnection($resource)
    {
        if(!($resource instanceof \PDO)) {
            throw new \InvalidArgumentException('Not a PDO connection');
        }

        return parent::setConnection($resource);
    }

    /**
     * Get the database name
     *
     * @return string	The database name
     */
    public function getDatabase()
    {
        return $this->_database;
    }

    /**
     * Set the database name
     *
     * @param 	string 	$database The database name
     * @return  DatabaseDriverAbstract
     *
     * @throws \RuntimeException
     */
    public function setDatabase($database)
    {
        $this->_database = $database;
        return $this;
    }

    /**
     * Retrieves the table schema information about the given table
     *
     * @param 	string 	$table A table name or a list of table names
     * @return	DatabaseSchemaTable
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
        return false;
    }

    /**
     * Unlocks tables
     *
     * @return  boolean  TRUE on success, FALSE otherwise.
     */
    public function unlockTable()
    {
        return false;
    }

    public function execute($query, $mode = Database::RESULT_STORE)
    {
        // Add or replace the database table prefix.
        if (!($query instanceof DatabaseQueryInterface)) {
            $query = $this->replaceTableNeedle($query);
        }

        try {
            $statement = $this->getConnection()->prepare((string)$query);

            $result = $statement->execute();

            if ($mode === Database::RESULT_STORE) {
                $this->_affected_rows = $statement->rowCount();
                $this->_insert_id = $this->getConnection()->lastInsertId();
            } else {
                $result = $statement;
            }

            return $result;
        } catch (\PDOException $e) {
            throw new \RuntimeException($e->getMessage() . ' of the following query : ' . $query, null, $e);
        }
    }

    /**
     * Close the given PDOStatement
     * @param \PDOStatement $statement
     */
    protected function _close(\PDOStatement $statement) {
        $statement->closeCursor();
        $statement = null;
    }

    /**
     * Execute a query
     *
     * @param  string      $query The query to run. Data inside the query should be properly escaped.
     * @param  integer     $mode  The result made, either the constant Database::RESULT_USE, Database::RESULT_STORE
     *                            or Database::MULTI_QUERY depending on the desired behavior.
     * @return mixed       For SELECT, SHOW, DESCRIBE or EXPLAIN will return a result object.
     *                     For other successful queries  return TRUE.
     */
    protected function _executeQuery($query, $mode = Database::RESULT_STORE)
    {
    }

    /**
     * Fetch the first field of the first row
     *
     * @param	\PDOStatement  	$result The result object. A result set identifier returned by the select() function
     * @param   integer         $key    The index to use
     * @return  mixed           The value returned in the query or null if the query failed.
     */
    protected function _fetchField($result, $key = 0)
    {
        $return = $result->fetchColumn((int) $key);

        $this->_close($result);

        return $return;
    }

    /**
     * Fetch an array of single field results
     *
     *
     * @param   \PDOStatement  	$result The result object. A result set identifier returned by the select() function
     * @param   integer         $key    The index to use
     * @return  array           A sequential array of returned rows.
     */
    protected function _fetchFieldList($result, $key = 0)
    {
        $array = array();

        while ($row = $result->fetch(\PDO::FETCH_NUM)) {
            $array[] = $row[(int)$key];
        }

        $this->_close($result);

        return $array;
    }

    /**
     * Fetch the first row of a result set as an associative array
     *
     * @param   \PDOStatement   $result The result object. A result set identifier returned by the select() function
     * @return array
     */
    protected function _fetchArray($result)
    {
        $object = $result->fetch(\PDO::FETCH_ASSOC);

        $this->_close($result);

        return $object;
    }

    /**
     * Fetch all result rows of a result set as an array of associative arrays
     *
     * If <var>key</var> is not empty then the returned array is indexed by the value of the database key.
     * Returns <var>null</var> if the query fails.
     *
     * @param   \PDOStatement   $result The result object. A result set identifier returned by the select() function
     * @param   string          $key    The column name of the index to use
     * @return  array   If key is empty as sequential list of returned records.
     */
    protected function _fetchArrayList($result, $key = '')
    {
        $array = [];

        if ($key) {
            while ($row = $result->fetch(\PDO::FETCH_ASSOC)) {
                $array[$row[$key]] = $row;
            }
        } else {
            $array = $result->fetchAll(\PDO::FETCH_ASSOC);
        }

        $this->_close($result);

        return $array;
    }

    /**
     * Fetch the first row of a result set as an object
     *
     * @param   \PDOStatement  $result The result object. A result set identifier returned by the select() function
     * @return  object
     */
    protected function _fetchObject($result)
    {
        $object = $result->fetch(\PDO::FETCH_OBJ);

        $result->closeCursor();
        $result = null;

        return $object;
    }

    /**
     * Fetch all rows of a result set as an array of objects
     *
     * If <var>key</var> is not empty then the returned array is indexed by the value of the database key.
     * Returns <var>null</var> if the query fails.
     *
     * @param   \PDOStatement  $result The result object. A result set identifier returned by the select() function
     * @param   string         $key    The column name of the index to use
     * @return  array   If <var>key</var> is empty as sequential array of returned rows.
     */
    protected function _fetchObjectList($result, $key='')
    {
        if ($key) {
            while ($row = $result->fetch(\PDO::FETCH_OBJ)) {
                $array[$row->$key] = $row;
            }
        } else {
            $array = $result->fetchAll(\PDO::FETCH_OBJ);
        }

        $this->_close($result);

        return $array;
    }

    /**
     * Safely quotes a value for an SQL statement.
     *
     * @param  mixed $value The value to quote
     * @param  mixed $type  Data type hint for drivers that have alternate quoting styles
     * @return string An SQL-safe quoted value
     */
    protected function _quoteValue($value, $type = \PDO::PARAM_STR)
    {
        return $this->getConnection()->quote($value, $type);
    }

    /**
     * Retrieves the table schema information about the given tables
     *
     * @param   string $table  A table name.
     * @return  DatabaseSchemaTable or null if the table doesn't exist.
     */
    protected function _fetchTableInfo($table)
    {
        $return = null;
        $query  = $this->getObject('lib:database.query.show', ['adapter' => $this])
            ->show('TABLE STATUS')
            ->like(':like')
            ->bind(array('like' => $table));

        if($info = $this->select($query, Database::FETCH_OBJECT)) {
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
        $query  = $this->getObject('lib:database.query.show', ['adapter' => $this])
            ->show('FULL COLUMNS')
            ->from($table);

        if($columns = $this->select($query, Database::FETCH_OBJECT_LIST))
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
        $query  = $this->getObject('lib:database.query.show', ['adapter' => $this])
            ->show('INDEX')
            ->from($table);

        if($indexes = $this->select($query, Database::FETCH_OBJECT_LIST))
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
     * @return  DatabaseSchemaTable
     */
    protected function _parseTableInfo($info)
    {
        $table              = new DatabaseSchemaTable();
        $table->name        = $info->Name;
        $table->engine      = $info->Engine;
        $table->type        = $info->Comment == 'VIEW' ? 'VIEW' : 'BASE';
        $table->length      = $info->Data_length;
        $table->autoinc     = $info->Auto_increment;
        $table->collation   = $info->Collation;
        $table->behaviors   = array();
        $table->description = $info->Comment != 'VIEW' ? $info->Comment : '';
        $table->modified    = $info->Update_time;

        return $table;
    }

    /**
     * Parse the raw column schema information
     *
     * @param   object  $info The raw column schema information
     * @return  DatabaseSchemaColumn
     */
    protected function _parseColumnInfo($info)
    {
        list($type, $length, $scope) = $this->_parseColumnType($info->Type);

        $column           = new DatabaseSchemaColumn();
        $column->name     = $info->Field;
        $column->type     = $type;
        $column->length   = $length ? $length : null;
        $column->scope    = $scope ? (int) $scope : null;
        $column->default  = $info->Default;
        $column->required = $info->Null != 'YES';
        $column->primary  = $info->Key == 'PRI';
        $column->unique   = ($info->Key == 'UNI' || $info->Key == 'PRI');
        $column->autoinc  = strpos($info->Extra, 'auto_increment') !== false;
        $column->filter   = (isset($this->_type_map[$type]) ? $this->_type_map[$type] : 'raw');

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

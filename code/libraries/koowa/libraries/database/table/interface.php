<?php
/**
 * Nooku Framework - http://nooku.org/framework
 *
 * @copyright   Copyright (C) 2007 - 2014 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/nooku/nooku-framework for the canonical source repository
 */

/**
 * Database Table Interface
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Koowa\Library\Database\Table
 */
interface KDatabaseTableInterface
{
    /**
     * Gets the database adapter
     *
     * @return KDatabaseAdapterInterface
     */
    public function getAdapter();

    /**
     * Set the database adapter
     *
     * @param  KDatabaseAdapterInterface $adapter A KDatabaseAdapterInterface object
     * @return KDatabaseQueryInterface
     */
    public function setAdapter(KDatabaseAdapterInterface $adapter);

    /**
     * Test the connected status of the table
     *
     * @return    boolean    Returns TRUE if we have a reference to a live KDatabaseAdapterAbstract object.
     */
    public function isConnected();

    /**
     * Gets the table schema name without the table prefix
     *
     * @return string
     */
    public function getName();

    /**
     * Gets the base table name without the table prefix
     *
     * If the table type is 'VIEW' the base name will be the name of the base table that is connected to the view.
     * If the table type is 'BASE' this function will return the same as {@link getName}
     *
     * @return string
     */
    public function getBase();

    /**
     * Gets the primary key(s) of the table
     *
     * @return array    An asscociate array of fields defined in the primary key
     */
    public function getPrimaryKey();

    /**
     * Gets the schema of the table
     *
     * @return  object|null Returns a KDatabaseSchemaTable object or NULL if the table doesn't exists
     */
    public function getSchema();

    /**
     * Get the table context
     *
     * @return  KCommand
     */
    public function getContext();

    /**
     * Check if the table column exists
     *
     * @param  string  $name The name of the column
     * @param  boolean $base If TRUE, get the column information from the base table. Default is FALSE.
     * @return bool  Returns TRUE if the column exists, FALSE otherwise.
     */
    public function hasColumn($name, $base = false);

    /**
     * Get a column by name
     *
     * @param  string  $name The name of the column
     * @param  boolean $base If TRUE, get the column information from the base table. Default is FALSE.
     * @return KDatabaseSchemaColumn  Returns a KDatabaseSchemaColumn object or NULL if the column does not exist
     */
    public function getColumn($name, $base = false);

    /**
     * Gets the columns for the table
     *
     * @param   boolean  $base If TRUE, get the column information from the base table.
     * @return  array    Associative array of KDatabaseSchemaColumn objects
     */
    public function getColumns($base = false);

    /**
     * Table map method
     *
     * This functions maps the column names to those in the table schema
     *
     * @param  array|string $data    An associative array of data to be mapped, or a column name
     * @param  boolean      $reverse If TRUE, perform a reverse mapping
     * @return array|string The mapped data or column name
     */
    public function mapColumns($data, $reverse = false);

    /**
     * Gets the identity column of the table.
     *
     * @return string
     */
    public function getIdentityColumn();

    /**
     * Set the identity column of the table.
     *
     * @param string $column The name of the identity column
     * @throws \DomainException If the column is not unique
     * @return KDatabaseTableAbstract
     */
    public function setIdentityColumn($column);

    /**
     * Gets the unique columns of the table
     *
     * @return array An associate array of unique table columns by column name
     */
    public function getUniqueColumns();

    /**
     * Get default values for all columns
     *
     * @return  array
     */
    public function getDefaults();

    /**
     * Get a default by name
     *
     * @param  string   $name Column name
     * @return mixed    Returns the column default value or NULL if the column does not exist
     */
    public function getDefault($name);

    /**
     * Set a default value for a column
     *
     * @param string   $column The name of the column
     * @param string   $value The value for the column
     * @return KDatabaseTableAbstract
     */
    public function setDefault($column, $value);

    /**
     * Get an instance of a row object for this table
     *
     * @param    array $options An optional associative array of configuration settings.
     * @return  KDatabaseRowInterface
     */
    public function createRow(array $options = array());

    /**
     * Get an instance of a rowset object for this table
     *
     * @param    array $options An optional associative array of configuration settings.
     * @return  KDatabaseRowInterface
     */
    public function createRowset(array $options = array());

    /**
     * Table select method
     *
     * This function will return an empty rowset if called without a parameter.
     *
     * @param mixed    $query KDatabaseQuery, query string, array of row id's, or an id or null
     * @param integer  $mode  The database fetch style.
     * @param array    $options An optional associative array of configuration options.
     * @return  KDatabaseRowInterface or KDatabaseRowsetInterface depending on the mode.
     */
    public function select($query = null, $mode = KDatabase::FETCH_ROWSET, array $options = array());

    /**
     * Count table rows
     *
     * @param   mixed   $query KDatabaseQuery object or query string or null to count all rows
     * @return  int     Number of rows
     */
    public function count($query = null);

    /**
     * Table insert method
     *
     * @param  KDatabaseRowInterface $row A KDatabaseRow object
     * @return bool|integer Returns the number of rows inserted, or FALSE if insert query was not executed.
     */
    public function insert(KDatabaseRowInterface $row);

    /**
     * Table update method
     *
     * @param  KDatabaseRowInterface $row  A KDatabaseRowInterface object
     * @return boolean|integer  Returns the number of rows updated, or FALSE if insert query was not executed.
     */
    public function update(KDatabaseRowInterface $row);

    /**
     * Table delete method
     *
     * @param  KDatabaseRowInterface $row A KDatabaseRow object
     * @return bool|integer Returns the number of rows deleted, or FALSE if delete query was not executed.
     */
    public function delete(KDatabaseRowInterface $row);

    /**
     * Lock the table.
     *
     * return boolean True on success, false otherwise.
     */
    public function lock();

    /**
     * Unlock the table.
     *
     * return boolean True on success, false otherwise.
     */
    public function unlock();

    /**
     * Table filter method
     *
     * This function removes extra columns based on the table columns taking any table mappings into account and
     * filters the data based on each column type.
     *
     * @param  array    $data An associative array of data to be filtered
     * @param  boolean  $base If TRUE, get the column information from the base table.
     * @return array    The filtered data
     */
    public function filter(array $data, $base = true);
}
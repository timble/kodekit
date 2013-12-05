<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright	Copyright (C) 2007 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/koowa for the canonical source repository
 */

/**
 * Database Rowset Interface
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Koowa\Library\Database
 */
interface KDatabaseRowsetInterface
{
    /**
     * Set the value of all the columns
     *
     * @param   string  $column The column name.
     * @param   mixed   $value The value for the property.
     * @return  void
     */
    public function set($column, $value);

    /**
     * Retrieve an array of column values
     *
     * @param   string  $column The column name.
     * @return  array   An array of all the column values
     */
    public function get($column);
	/**
     * Returns all data as an array.
     *
     * @param   boolean $modified If TRUE, only return the modified data. Default FALSE
     * @return array
     */
    public function getData($modified = false);

	/**
  	 * Set the rowset data based on a named array/hash
  	 *
  	 * @param   mixed 	$data       Either and associative array, a KDatabaseRow object or object
  	 * @param   boolean $modified   If TRUE, update the modified information for each column being set. Default TRUE
 	 * @return 	KDatabaseRowsetAbstract
  	 */
  	 public function setData( $data, $modified = true );

    /**
     * Add rows to the rowset
     *
     * @param  array   $rows    An associative array of row data to be inserted.
     * @param  string  $status  The row(s) status
     * @return KDatabaseRowsetInterface
     * @see __construct
     */
    public function addRow(array $rows, $status = null);

    /**
     * Returns the status message
     *
     * @return string The status message
     */
    public function getStatusMessage();

    /**
     * Set the status message
     *
     * @param   string $message The status message
     * @return  KDatabaseRowsetInterface
     */
    public function setStatusMessage($message);

    /**
	 * Gets the identity column of the rowset
	 *
	 * @return string
	 */
	public function getIdentityColumn();

	/**
     * Returns a KDatabaseRow
     *
     * This functions accepts either a know position or associative
     * array of key/value pairs
     *
     * @param 	string 	$needle     The position or the key to search for
     * @return KDatabaseRowAbstract
     */
    public function find($needle);

	/**
     * Saves all rows in the rowset to the database
     *
     * @return KDatabaseRowsetAbstract
     */
    public function save();

	/**
     * Deletes all rows in the rowset from the database
     *
     * @return KDatabaseRowsetAbstract
     */
    public function delete();

	/**
     * Reset the rowset
     *
     * @return KDatabaseRowsetAbstract
     */
    public function reset();

	/**
     * Insert a row in the rowset
     *
     * The row will be stored by its identity_column if set or otherwise by it's object handle.
     *
     * @param  KDatabaseRowInterface|KObjectHandlable 	$row A KDatabaseRow object to be inserted
     * @return KDatabaseRowsetAbstract
     */
    public function insert(KObjectHandlable $row);

	/**
     * Removes a row
     *
     * The row will be removed based on its identity_column if set or otherwise by
     * it's object handle.
     *
     * @param  KDatabaseRowInterface|KObjectHandlable $row 	A KDatabaseRow object to be removed
     * @return KDatabaseRowsetAbstract
     */
    public function extract(KObjectHandlable $row);

    /**
	 * Test the connected status of the rowset.
	 *
	 * @return	bool
	 */
    public function isConnected();

    /**
     * Return an associative array of the data.
     *
     * @return array
     */
    public function toArray();
}

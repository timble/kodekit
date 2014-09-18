<?php
/**
 * Nooku Framework - http://nooku.org/framework
 *
 * @copyright   Copyright (C) 2007 - 2014 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/nooku/nooku-framework for the canonical source repository
 */

/**
 * Table Database Schema
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Koowa\Library\Database\Schema
 */
class KDatabaseSchemaTable
{
    /**
     * Table name
     *
     * @var string
     */
    public $name;

    /**
     * The storage engine
     *
     * @var string
     */
    public $engine;

    /**
     * Table type
     *
     * @var	string
     */
    public $type;

    /**
     * Table length
     *
     * @var integer
     */
    public $length;

    /**
     * Table next auto increment value
     *
     * @var integer
     */
    public $autoinc;

    /**
     * The tables character set and collation
     *
     * @var string
     */
    public $collation;

    /**
     * The tables description
     *
     * @var string
     */
    public $description;

    /**
     * List of columns
     *
     * Associative array of columns, where key holds the columns name and the value is an KDatabaseSchemaColumn
     * object.
     *
     * @var	array
     */
    public $columns = array();

    /**
     * List of behaviors
     *
     * Associative array of behaviors, where key holds the behavior identifier string and the value is an
     * KDatabaseBehavior object.
     *
     * @var	array
     */
    public $behaviors = array();

    /**
     * List of indexes
     *
     * Associative array of indexes, where key holds the index name and the and the value is an object.
     *
     * @var	array
     */
    public $indexes = array();
}

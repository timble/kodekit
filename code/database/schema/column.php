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
 * Column Database Schema
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Kodekit\Library\Database\Schema
 */
class DatabaseSchemaColumn
{
    /**
     * Column name
     *
     * @var string
     */
    public $name;

    /**
     * Column type
     *
     * @var	string
     */
    public $type;

    /**
     * Column length
     *
     * @var integer
     */
    public $length;

    /**
     * Column scope
     *
     * @var string
     */
    public $scope;

    /**
     * Column default value
     *
     * @var string
     */
    public $default;

    /**
     * Required column
     *
     * @var bool
     */
    public $required = false;

    /**
     * Is the column a primary key
     *
     * @var bool
     */
    public $primary = false;

    /**
     * Is the column autoincremented
     *
     * @var	bool
     */
    public $autoinc = false;

    /**
     * Is the column unique
     *
     * @var	bool
     */
    public $unique = false;

    /**
     * Related index columns
     *
     * @var	bool
     */
    public $related = array();

    /**
     * Filter
     *
     * @var	string
     */
    public $filter;
}

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
 * Insert Database Query
 *
 * @author  Gergo Erdosi <https://github.com/gergoerdosi>
 * @package Kodekit\Library\Database\Query
 */
class DatabaseQueryInsert extends DatabaseQueryAbstract
{
    /**
     * The table name.
     *
     * @var string
     */
    public $table;

    /**
     * Update type
     *
     * Possible values are INSERT|REPLACE|INSERT IGNORE
     *
     * @var string
     */
    public $type = 'INSERT';

    /**
     * Array of column names.
     *
     * @var array
     */
    public $columns = array();

    /**
     * Array of values.
     *
     * @var array
     */
    public $values = array();

    /**
     * Array of values for the update statement coming after ON DUPLICATE KEY UPDATE
     *
     * @var array
     */
    public $duplicate_key_values = array();

    /**
     * Sets insert operation type
     *
     * Possible values are INSERT|REPLACE|INSERT IGNORE
     *
     * @param string $type
     * @return $this
     */
    public function type($type)
    {
        $type = strtoupper($type);

        if (!in_array($type, ['INSERT', 'INSERT IGNORE', 'REPLACE'])) {
            throw new \UnexpectedValueException('Invalid insert type');
        }

        $this->type = $type;

        return $this;
    }

    /**
     * Runs the operation as a REPLACE
     *
     * @return $this
     */
    public function replace()
    {
        $this->type('REPLACE');

        return $this;
    }

    /**
     * Runs the operation as INSERT IGNORE
     *
     * @return $this
     */
    public function ignore()
    {
        $this->type('INSERT IGNORE');

        return $this;
    }

    /**
     * Adds an ON DUPLICATE KEY VALUES clause to the end of the query
     *
     * @link https://dev.mysql.com/doc/refman/5.7/en/insert-on-duplicate.html
     * @param $values
     * @return $this
     */
    public function onDuplicateKey($values)
    {
        $this->duplicate_key_values = array_merge($this->duplicate_key_values, (array) $values);

        return $this;
    }

    /**
     * Build the table clause
     *
     * @param  string $table The table name.
     * @return DatabaseQueryInsert
     */
    public function table($table)
    {
        $this->table = $table;

        return $this;
    }

    /**
     * Build the columns clause
     *
     * @param  array $columns Array of column names.
     * @return DatabaseQueryInsert
     */
    public function columns(array $columns)
    {
        $this->columns = $columns;

        return $this;
    }

    /**
     * Build the values clause
     *
     * @param  array $values Array of values.
     * @return DatabaseQueryInsert
     */
    public function values($values)
    {
        if(!$values instanceof DatabaseQuerySelect)
        {
            if (!$this->columns && !is_numeric(key($values))) {
                $this->columns(array_keys($values));
            }

            $this->values[] = array_values($values);
        }
        else $this->values = $values;

        return $this;
    }

    /**
     * Render the query to a string.
     *
     * @return  string  The query string.
     */
    public function toString()
    {
        $driver = $this->getDriver();
        $prefix = $driver->getTablePrefix();
        $query   = $this->type;

        if($this->table) {
            $query .= ' INTO '.$driver->quoteIdentifier($prefix.$this->table);
        }

        if($this->columns) {
            $query .= '('.implode(', ', array_map(array($driver, 'quoteIdentifier'), $this->columns)).')';
        }

        if($this->values)
        {
            if(!$this->values instanceof DatabaseQuerySelect)
            {
                $query .= ' VALUES'.PHP_EOL;

                $values = array();
                foreach ($this->values as $row)
                {
                    $data = array();
                    foreach($row as $column) {
                        $data[] = $driver->quoteValue(is_object($column) ? (string) $column : $column);
                    }

                    $values[] = '('.implode(', ', $data).')';
                }

                $query .= implode(', '.PHP_EOL, $values);
            }
            else $query .= ' '.$this->values;
        }

        if($this->duplicate_key_values && $this->type === 'INSERT')
        {
            $values = array();
            foreach($this->duplicate_key_values as $value) {
                $values[] = ' '. $adapter->quoteIdentifier($value);
            }

            $query .= ' ON DUPLICATE KEY UPDATE '.implode(', ', $values);
        }

        if($this->_parameters) {
            $query = $this->_replaceParams($query);
        }

        return $query;
    }
}

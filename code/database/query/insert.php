<?php
/**
 * Kodekit - http://timble.net/kodekit
 *
 * @copyright   Copyright (C) 2007 - 2016 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
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
        $query  = 'INSERT';

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

        return $query;
    }
}

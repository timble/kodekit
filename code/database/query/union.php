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
 * Union Database Query
 *
 * @author  Ercan Ozkaya <https://github.com/ercanozkaya>
 * @package Kodekit\Library\Database\Query
 */
class DatabaseQueryUnion extends DatabaseQuerySelect
{
    /**
     * Queries
     */
    public $queries = [];

    /**
     * UNION DISTINCT operation
     *
     * @var boolean
     */
    public $distinct  = false;

    /**
     * UNION ALL operation
     *
     * @var boolean
     */
    public $all = false;

    /**
     * Add queries for the UNION operation
     *
     * @param DatabaseQuerySelect $query
     * @return $this
     */
    public function union(DatabaseQuerySelect $query)
    {
        $this->queries[] = $query;

        return $this;
    }

    /**
     * Checks if the current query should use UNION ALL
     *
     * @return boolean
     */
    public function isUnionAllQuery()
    {
        return (bool) $this->all;
    }

    /**
     * Make the query use UNION ALL
     *
     * @return $this
     */
    public function all()
    {
        $this->all = true;

        return $this;
    }

    /**
     * Set columns in all queries
     *
     * @param array $columns
     * @return DatabaseQuerySelect|void
     */
    public function columns($columns = array())
    {
        foreach ($this->queries as $query) {
            $query->where($columns);
        }
    }

    /**
     * Set tables in all queries
     *
     * @param $table
     * @return DatabaseQuerySelect|void
     */
    public function table($table)
    {
        foreach ($this->queries as $query) {
            $query->table($table);
        }
    }

    /**
     * Set joins in all queries
     *
     * @param string $table
     * @param null   $condition
     * @param string $type
     * @return $this|DatabaseQuerySelect
     */
    public function join($table, $condition = null, $type = 'LEFT')
    {
        foreach ($this->queries as $query) {
            $query->where($table, $condition, $type);
        }

        return $this;
    }

    /**
     * Set where clauses in all queries
     *
     * @param string $condition
     * @param string $combination
     * @return $this|DatabaseQuerySelect
     */
    public function where($condition, $combination = 'AND')
    {
        foreach ($this->queries as $query) {
            $query->where($condition, $combination);
        }

        return $this;
    }

    /**
     * Set groups in all queries
     *
     * @param array|string $columns
     * @return $this|DatabaseQuerySelect
     */
    public function group($columns)
    {
        foreach ($this->queries as $query) {
            $query->group($columns);
        }

        return $this;
    }

    /**
     * Set having constraints in all queries
     * @param   string $condition   The having condition statement
     * @param   string $combination The having combination, defaults to 'AND'
     * @return $this|DatabaseQuerySelect
     */
    public function having($condition, $combination = 'AND')
    {
        foreach ($this->queries as $query) {
            $query->having($condition, $combination);
        }

        return $this;
    }

    /**
     * Render the query to a string
     *
     * @return  string  The completed query
     * @throws \RuntimeException When there are less than 2 queries to combine
     */
    public function toString()
    {
        if (count($this->queries) < 2) {
            throw new \RuntimeException("Union needs at least 2 SELECT queries");
        }

        $queries = [];

        foreach ($this->queries as $query) {
            $queries[] = '('.$query->toString().')';
        }

        $driver = $this->getDriver();
        $glue   = $this->all ? 'UNION ALL' : ($this->distinct ? 'UNION DISTINCT'  : 'UNION');
        $query  = implode("\n".$glue."\n", $queries);

        if($this->order)
        {
            $query .= ' ORDER BY ';

            $list = array();
            foreach($this->order as $order) {
                $list[] = $driver->quoteIdentifier($order['column']).' '.$order['direction'];
            }

            $query .= implode(' , ', $list);
        }

        if($this->limit) {
            $query .= ' LIMIT '.$this->offset.' , '.$this->limit;
        }

        if($this->_parameters) {
            $query = $this->_replaceParams($query);
        }

        return $query;
    }
}

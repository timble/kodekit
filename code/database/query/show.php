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
 * Show Database Query
 *
 * @author  Gergo Erdosi <https://github.com/gergoerdosi>
 * @package Kodekit\Library\Database\Query
 */
class DatabaseQueryShow extends DatabaseQueryAbstract
{
    /**
     * The show clause.
     *
     * @var string
     */
    public $show;

    /**
     * The from clause.
     *
     * @var string
     */
    public $from;

    /**
     * The like clause.
     *
     * @var string
     */
    public $like;

    /**
     * The where clause.
     *
     * @var array
     */
    public $where = array();

    /**
     * Build the show clause
     *
     * @param   string $table The name of the table.
     * @return  DatabaseQueryShow
     */
    public function show($table)
    {
        $this->show = $table;
        return $this;
    }

    /**
     * Build the from clause
     *
     * @param   string $from The name of the database or table.
     * @return  $this
     */
    public function from($from)
    {
        $this->from = $from;
        return $this;
    }

    /**
     * Build the like clause
     *
     * @param   string $pattern The pattern to match.
     * @return  DatabaseQueryShow
     */
    public function like($pattern)
    {
        $this->like = $pattern;

        return $this;
    }

    /**
     * Build the where clause
     *
     * @param   string $condition   The condition.
     * @param   string $combination Combination type, defaults to 'AND'.
     * @return  DatabaseQueryShow
     */
    public function where($condition, $combination = 'AND')
    {
        $this->where[] = array(
            'condition'   => $condition,
            'combination' => count($this->where) ? $combination : ''
        );

        return $this;
    }

    /**
     * Render the query to a string.
     *
     * @return  string  The query string.
     */
    public function toString()
    {
        $driver  = $this->getDriver();
        $prefix  = $driver->getTablePrefix();
        $query   = 'SHOW '.$this->show;

        if($this->from)
        {
            $table  = (in_array($this->show, array('FULL COLUMNS', 'COLUMNS', 'INDEX', 'INDEXES', 'KEYS')) ? $prefix : '').$this->from;
            $query .= ' FROM '.$driver->quoteIdentifier($table);
        }

        if($this->like) {
            $query .= ' LIKE '.$driver->quoteIdentifier($this->like);
        }

        if($this->where)
        {
            $query .= ' WHERE';

            foreach($this->where as $where)
            {
                if(!empty($where['combination'])) {
                    $query .= ' '.$where['combination'];
                }

                $query .= ' '.$driver->quoteIdentifier($where['condition']);
            }
        }

        if($this->_parameters) {
            $query = $this->_replaceParams($query);
        }

        return $query;
    }

    /**
     * Callback method for parameter replacement.
     *
     * @param  array  $matches Matches of preg_replace_callback.
     * @return string The replacement string.
     */
    protected function _replaceParamsCallback($matches)
    {
        $key    = substr($matches[0], 1);
        $prefix = '';

        if(in_array($this->show, array('FULL TABLES', 'OPEN TABLES', 'TABLE STATUS', 'TABLES')) &&
            ($this->like && $key == 'like' || $this->where && ($key == 'name' || $key == 'table')))
        {
            $prefix = $this->getDriver()->getTablePrefix();
        }

        $replacement = $this->getDriver()->quoteValue($prefix.$this->_parameters[$key]);

        return is_array($this->_parameters[$key]) ? '('.$replacement.')' : $replacement;
    }
}

<?php
/**
 * Nooku Framework - http://nooku.org/framework
 *
 * @copyright   Copyright (C) 2007 - 2014 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/nooku/nooku-framework for the canonical source repository
 */

/**
 * Templates model that wraps Joomla template data
 *
 * @author  Dave Li <https://github.com/daveli>
 * @package Koowa\Component\Koowa\Model
 */
class ComKoowaModelTemplates extends KModelDatabase
{
    public function __construct(KObjectConfig $config)
    {
        parent::__construct($config);

        $this->getState()
            ->insert('active', 'int')
            ->insert('sort', 'cmd', 'client_id')
            ->insert('direction', 'word', 'asc');
    }

    protected function _buildQueryWhere(KDatabaseQueryInterface $query)
    {
        parent::_buildQueryWhere($query);

        $state = $this->getState();

        if (is_numeric($state->active)) {
            $query->where('tbl.home = :home')->bind(array('home' => 1));
        }
    }
}

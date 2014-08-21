<?php
/**
 * Nooku Framework - http://nooku.org/framework
 *
 * @copyright   Copyright (C) 2007 - 2014 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/nooku/nooku-framework for the canonical source repository
 */

/**
 * Users model that wraps Joomla user data
 *
 * @author  Arunas Mazeika <https://github.com/amazeika>
 * @package Koowa\Component\Koowa\Model
 */
class ComKoowaModelUsers extends KModelDatabase
{
    public function __construct(KObjectConfig $config)
    {
        parent::__construct($config);

        $this->getState()
            ->insert('email'   , 'email', null, true)
            ->insert('username', 'alnum', null, true);
    }

    protected function _initialize(KObjectConfig $config)
    {
        $config->append(array(
            'table'     => $this->getIdentifier()->name,
            'behaviors' => array('searchable' => array('columns' => array('name', 'username', 'email')))
        ));

        parent::_initialize($config);
    }
}
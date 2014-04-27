<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright	Copyright (C) 2007 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/koowa for the canonical source repository
 */

/**
 * Users model that wraps Joomla user data
 *
 * @author  Arunas Mazeika <https://github.com/amazeika>
 * @package Koowa\Component\Koowa
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
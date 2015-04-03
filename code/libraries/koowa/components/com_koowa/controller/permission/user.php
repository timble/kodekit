<?php
/**
 * Nooku Framework - http://nooku.org/framework
 *
 * @copyright   Copyright (C) 2007 - 2014 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/nooku/nooku-framework for the canonical source repository
 */

/**
 * Error Controller Permission
 *
 * @author  Johan Janssens <http://github.com/johanjanssens>
 * @package Koowa\Wordpress\Library\Controller
 */
class ComKoowaControllerPermissionUser extends KControllerPermissionAbstract
{
    public function canAdd()
    {
        return false;
    }

    public function canEdit()
    {
        return false;
    }

    public function canDelete()
    {
        return false;
    }

    public function canAdmin()
    {
        return false;
    }

    public function canManage()
    {
        return false;
    }
}
<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright	Copyright (C) 2007 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/koowa for the canonical source repository
 */

/**
 * Abstract Controller Permission
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Koowa\Library\Controller
 */
abstract class ComKoowaControllerPermissionAbstract extends KControllerPermissionAbstract
{
    /**
     * {@inheritdoc}
     */
    public function canAdd()
    {
        return (parent::canAdd() && JFactory::getUser()->authorise('core.create') === true);
    }

    /**
     * {@inheritdoc}
     */
    public function canEdit()
    {
        return (parent::canEdit() && JFactory::getUser()->authorise('core.edit') === true);
    }

    /**
     * {@inheritdoc}
     */
    public function canDelete()
    {
        return (parent::canDelete() && JFactory::getUser()->authorise('core.delete') === true);
    }

    /**
     * Check if user can perform administrative tasks such as changing configuration options
     *
     * @return  boolean  Can return both true or false.
     */
    public function canAdmin()
    {
        $component = $this->getIdentifier()->package;

        return JFactory::getUser()->authorise('core.admin', 'com_'.$component) === true;
    }

    /**
     * Check if user can can access a component in the administrator backend
     *
     * @return  boolean  Can return both true or false.
     */
    public function canManage()
    {
        $component = $this->getIdentifier()->package;

        return JFactory::getUser()->authorise('core.manage', 'com_'.$component) === true;
    }
}
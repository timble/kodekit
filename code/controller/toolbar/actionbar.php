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
 * Action Controller Toolbar
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Kodekit\Library\Controller\Toolbar
 */
class ControllerToolbarActionbar extends ControllerToolbarAbstract
{
    /**
     * Initializes the config for the object
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param   ObjectConfig $config Configuration options
     * @return  void
     */
    protected function _initialize(ObjectConfig $config)
    {
        $config->append(array(
            'type'  => 'actionbar',
        ));

        parent::_initialize($config);
    }

    /**
     * Add default toolbar commands and set the toolbar title
     * .
     * @param ControllerContext	$context A controller context object
     */
    protected function _afterRead(ControllerContext $context)
    {
        $controller = $this->getController();

        if($controller->isEditable() && $controller->canSave()) {
            $this->addCommand('save');
        }

        if($controller->isEditable() && $controller->canApply()) {
            $this->addCommand('apply');
        }

        if($controller->isEditable() && $controller->canCancel()) {
            $this->addCommand('cancel',  array('attribs' => array('data-novalidate' => 'novalidate')));
        }
    }

    /**
     * Add default action commands
     * .
     * @param ControllerContext $context A command context object
     */
    protected function _afterBrowse(ControllerContext $context)
    {
        $controller = $this->getController();

        if($controller->canAdd()) {
            $this->addCommand('new');
        }

        if($controller->canDelete()) {
            $this->addCommand('delete');
        }
    }

    /**
     * New toolbar command
     *
     * @param   ControllerToolbarCommand $command  A ControllerToolbarCommand object
     * @return  void
     */
    protected function _commandNew(ControllerToolbarCommand $command)
    {
        if (empty($command->href))
        {
            $identifier    = $this->getController()->getIdentifier();
            $command->href = 'component='.$identifier->package.'&view='.$identifier->name;
        }

        $command->icon = 'k-icon-plus';
    }

    /**
     * Delete toolbar command
     *
     * @param   ControllerToolbarCommand $command  A KControllerToolbarCommand object
     * @return  void
     */
    protected function _commandDelete(ControllerToolbarCommand $command)
    {
        $translator = $this->getObject('translator');
        $command->append([
            'data' => [
                'action' => 'delete',
                'prompt' => $translator->translate('Deleted items will be lost forever. Would you like to continue?')
            ]
        ]);

        $command->icon = 'k-icon-trash';
    }

    /**
     * Edit toolbar command
     *
     * @param   ControllerToolbarCommand $command  A KControllerToolbarCommand object
     * @return  void
     */
    protected function _commandSave(ControllerToolbarCommand $command)
    {
        $command->icon = 'k-icon-check';

        $command->append([
            'data' => [
                'action' => 'save',
            ]
        ]);
    }

    /**
     * Edit toolbar command
     *
     * @param   ControllerToolbarCommand $command  A KControllerToolbarCommand object
     * @return  void
     */
    protected function _commandApply(ControllerToolbarCommand $command)
    {
        $command->icon = 'k-icon-pencil';

        $command->append([
            'data' => [
                'action' => 'apply',
            ]
        ]);
    }

    /**
     * Disable Toolbar Command
     *
     * @param   ControllerToolbarCommand $command  A KControllerToolbarCommand object
     * @return  void
     */
    protected function _commandSave2new(ControllerToolbarCommand $command)
    {
        $command->icon = 'k-icon-plus k-icon--success';

        $command->append([
            'data' => [
                'action' => 'save2new',
            ]
        ]);
    }

    /**
     * Cancel Toolbar Command
     *
     * @param   ControllerToolbarCommand $command  A KControllerToolbarCommand object
     * @return  void
     */
    protected function _commandCancel(ControllerToolbarCommand $command)
    {
        $command->icon = 'k-icon-x k-icon--error';

        $command->append([
            'data' => [
                'action' => 'cancel',
                'novalidate' => 'novalidate',
            ]
        ]);
    }

    /**
     * Enable toolbar command
     *
     * @param   ControllerToolbarCommand $command  A ControllerToolbarCommand object
     * @return  void
     */
    protected function _commandEnable(ControllerToolbarCommand $command)
    {
        $command->icon = 'k-icon-check k-icon--success';

        $command->append(array(
            'data' => array(
                'action' => 'edit',
                'data'   => array('enabled' => 1)
            )
        ));
    }

    /**
     * Disable toolbar command
     *
     * @param   ControllerToolbarCommand $command  A ControllerToolbarCommand object
     * @return  void
     */
    protected function _commandDisable(ControllerToolbarCommand $command)
    {
        $command->icon = 'k-icon-x k-icon--error';

        $command->append(array(
            'data' => array(
                'action' => 'edit',
                'data'   => array('enabled' => 0)
             )
        ));
    }

    /**
     * Export Toolbar Command
     *
     * @param   ControllerToolbarCommand $command  A ControllerToolbarCommand object
     * @return  void
     */
    protected function _commandExport(ControllerToolbarCommand $command)
    {
        //Get the states
        $states = $this->getController()->getModel()->getState()->getValues();

        unset($states['limit']);
        unset($states['offset']);

        $states['format'] = 'csv';

        //Get the query options
        $query  = http_build_query($states, '', '&');
        $option = $this->getIdentifier()->package;
        $view   = $this->getController()->getView()->getName();

        $command->href = 'component='.$option.'&view='.$view.'&'.$query;
    }

    /**
     * Modal toolbar command
     *
     * @param   ControllerToolbarCommand $command  A ControllerToolbarCommand object
     * @return  void
     */
    protected function _commandDialog(ControllerToolbarCommand $command)
    {
        $command->append(array(
            'href' => ''
        ))->append(array(
            'attribs' => array(
                'class' => array('k-modal'),
                'href'  => $command->href,
            ),
            'data' => array(
                'k-modal' => array('type' => 'iframe')
            )
        ));
    }
}

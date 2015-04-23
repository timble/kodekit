<?php
/**
 * Nooku Framework - http://nooku.org/framework
 *
 * @copyright   Copyright (C) 2007 - 2014 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/nooku/nooku-framework for the canonical source repository
 */

/**
 * Action Controller Toolbar
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Koowa\Library\Controller\Toolbar
 */
abstract class KControllerToolbarActionbar extends KControllerToolbarAbstract
{
    /**
     * Constructor
     *
     * @param   KObjectConfig $config Configuration options
     */
    public function __construct(KObjectConfig $config = null)
    {
        parent::__construct($config);

        //Add a title command
        $this->addTitle($config->title, $config->icon);
    }

    /**
     * Initializes the config for the object
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param   KObjectConfig $config Configuration options
     * @return  void
     */
    protected function _initialize(KObjectConfig $config)
    {
        $config->append(array(
            'type'  => 'actionbar',
        ));

        parent::_initialize($config);
    }

    /**
     * Add a separator command
     *
     * @return  KControllerToolbarAbstract
     */
    public function addSeparator()
    {
        $this->_commands[] = new KControllerToolbarCommand('separator');
        return $this;
    }

    /**
     * Add default toolbar commands and set the toolbar title
     * .
     * @param KControllerContextInterface	$context A controller context object
     */
    protected function _afterRead(KControllerContextInterface $context)
    {
        $controller = $this->getController();

        if($controller->isEditable() && $controller->canApply()) {
            $this->addCommand('apply');
        }

        if($controller->isEditable() && $controller->canSave()) {
            $this->addCommand('save');
        }

        if($controller->isEditable() && $controller->canCancel()) {
            $this->addCommand('cancel',  array('attribs' => array('data-novalidate' => 'novalidate')));
        }
    }

    /**
     * Add default action commands
     * .
     * @param KControllerContextInterface $context A command context object
     */
    protected function _afterBrowse(KControllerContextInterface $context)
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
     * @param   KControllerToolbarCommand $command  A ControllerToolbarCommand object
     * @return  void
     */
    protected function _commandNew(KControllerToolbarCommand $command)
    {
        if (empty($command->href))
        {
            $identifier    = $this->getController()->getIdentifier();
            $command->href = 'component='.$identifier->package.'&view='.$identifier->name;
        }
    }

    /**
     * Delete toolbar command
     *
     * @param   KControllerToolbarCommand $command  A KControllerToolbarCommand object
     * @return  void
     */
    protected function _commandDelete(KControllerToolbarCommand $command)
    {
        $translator = $this->getObject('translator');
        $command->append(array(
            'attribs' => array(
                'data-action' => 'delete',
                'data-prompt' => $translator->translate('Deleted items will be lost forever. Would you like to continue?')
            )
        ));
    }

    /**
     * Enable toolbar command
     *
     * @param   KControllerToolbarCommand $command  A KControllerToolbarCommand object
     * @return  void
     */
    protected function _commandEnable(KControllerToolbarCommand $command)
    {
        $command->icon = 'icon-32-publish';

        $command->append(array(
            'attribs' => array(
                'data-action' => 'edit',
                'data-data'   => '{"enabled":1}'
            )
        ));
    }

    /**
     * Disable toolbar command
     *
     * @param   KControllerToolbarCommand $command  A KControllerToolbarCommand object
     * @return  void
     */
    protected function _commandDisable(KControllerToolbarCommand $command)
    {
        $command->icon = 'icon-32-unpublish';

        $command->append(array(
            'attribs' => array(
                'data-action' => 'edit',
                'data-data'   => '{"enabled":0}'
            )
        ));
    }

    /**
     * Export Toolbar Command
     *
     * @param   KControllerToolbarCommand $command  A KControllerToolbarCommand object
     * @return  void
     */
    protected function _commandExport(KControllerToolbarCommand $command)
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

        $command->href = 'option=com_'.$option.'&view='.$view.'&'.$query;
    }

    /**
     * Modal toolbar command
     *
     * @param   KControllerToolbarCommand $command  A KControllerToolbarCommand object
     * @return  void
     */
    protected function _commandDialog(KControllerToolbarCommand $command)
    {
        $command->append(array(
            'href'	  => ''
        ))->append(array(
                'attribs' => array(
                    'class' => array('koowa-modal'),
                    'href'  => $command->href,
                    'data-koowa-modal'   => array('type' => 'iframe')
                )
            ));

        $command->attribs['data-koowa-modal'] = json_encode($command->attribs['data-koowa-modal']);
    }
}

<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright	Copyright (C) 2007 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/koowa for the canonical source repository
 */


/**
 * Action Controller Toolbar
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Koowa\Component\Koowa
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
            'title' => KStringInflector::humanize($this->getName()),
            'icon'  => $this->getName(),
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
     * Add a title command
     *
     * @param   string $title   The title
     * @param   string $icon    The icon
     * @return  KControllerToolbarAbstract
     */
    public function addTitle($title, $icon = '')
    {
        $this->_commands[] = new KControllerToolbarCommand('title', array(
            'title' => $title,
            'icon'  => $icon
        ));
        return $this;
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
                'data-data'   => '{enabled:1}'
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
                'data-data'   => '{enabled:0}'
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
        $states = $this->getController()->getModel()->getState()->toArray();

        unset($states['limit']);
        unset($states['offset']);

        $states['format'] = 'csv';

        //Get the query options
        $query  = http_build_query($states, '', '&');
        $option = $this->getIdentifier()->package;
        $view   = $this->getIdentifier()->name;

        $command->append(array(
            'attribs' => array(
                'href' =>  JRoute::_('index.php?option=com_'.$option.'&view='.$view.'&'.$query)
            )
        ));
    }

    /**
     * Modal toolbar command
     *
     * @param   KControllerToolbarCommand $command  A KControllerToolbarCommand object
     * @return  void
     */
    protected function _commandModal(KControllerToolbarCommand $command)
    {
        $command->append(array(
            'width'   => '640',
            'height'  => '480',
            'href'	  => ''
        ))->append(array(
                'attribs' => array(
                    'class' => array('modal'),
                    'href'  => $command->href,
                    'rel'   => '{handler: \'iframe\', size: {x: '.$command->width.', y: '.$command->height.'}}'
                )
            ));
    }

    /**
     * Add default action commands and set the action bar title
     * .
     * @param	KCommandContext	$context A command context object
     */
    protected function _afterRead(KCommandContext $context)
    {
        $controller = $this->getController();
        $name       = ucfirst($context->caller->getIdentifier()->name);

        if($controller->getModel()->getState()->isUnique())
        {
            $saveable = $controller->canEdit();
            $title    = 'Edit '.$name;
        }
        else
        {
            $saveable = $controller->canAdd();
            $title    = 'New '.$name;
        }

        if($saveable)
        {
            $this->getCommand('title')->title = $title;
            $this->addCommand('save');
            $this->addCommand('apply');
        }

        $this->addCommand('cancel',  array('attribs' => array('data-novalidate' => 'novalidate')));
    }

    /**
     * Add default action commands
     * .
     * @param	KCommandContext	$context A command context object
     */
    protected function _afterBrowse(KCommandContext $context)
    {
        $controller = $this->getController();

        if($controller->canAdd())
        {
            $identifier = $context->caller->getIdentifier();

            $this->addCommand('new', array(
                'href' => JRoute::_('index.php?option=com_'.$identifier->package.'&view='.$identifier->name)
            ));
        }

        if($controller->canDelete()) {
            $this->addCommand('delete');
        }
    }
}

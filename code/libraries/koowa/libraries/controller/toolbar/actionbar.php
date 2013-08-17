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
     * The toolbar title
     *
     * @var     string
     */
    protected $_title = '';

    /**
     * The toolbar icon
     *
     * @var     string
     */
    protected $_icon = '';

    /**
     * Constructor
     *
     * @param   KConfig $config Configuration options
     */
    public function __construct(KConfig $config = null)
    {
        parent::__construct($config);

        // Set the title
        $this->setTitle($config->title);

        // Set the icon
        $this->setIcon($config->icon);
    }

    /**
     * Initializes the config for the object
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param   KConfig $config Configuration options
     * @return  void
     */
    protected function _initialize(KConfig $config)
    {
        $config->append(array(
            'type'  => 'actionbar',
            'title' => KInflector::humanize($this->getName()),
            'icon'  => $this->getName(),
        ));

        parent::_initialize($config);
    }

    /**
     * Set the toolbar's title
     *
     * @param   string  $title Title
     * @return  KControllerToolbarAbstract
     */
    public function setTitle($title)
    {
        $this->_title = $title;
        return $this;
    }

    /**
     * Get the toolbar's title
     *
     * @return   string  Title
     */
    public function getTitle()
    {
        return $this->_title;
    }

    /**
     * Set the toolbar's icon
     *
     * @param   string  $icon Icon
     * @return  KControllerToolbarAbstract
     */
    public function setIcon($icon)
    {
        $this->_icon = $icon;
        return $this;
    }

    /**
     * Get the toolbar's icon
     *
     * @return   string  Icon
     */
    public function getIcon()
    {
        return $this->_icon;
    }

    /**
     * Add a separator
     *
     * @return  KControllerToolbarAbstract
     */
    public function addSeparator()
    {
        $this->_commands[] = new KControllerToolbarCommand('separator');
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
            $this->setTitle($title)
                 ->addCommand('save')
                 ->addCommand('apply');
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
            $config     = array('attribs' => array(
                'href' => JRoute::_( 'index.php?option=com_'.$identifier->package.'&view='.$identifier->name)
            ));

            $this->addCommand('new', $config);
        }

        if($controller->canDelete()) {
            $this->addCommand('delete');
        }
    }
}

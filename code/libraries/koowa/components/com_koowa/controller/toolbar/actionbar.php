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
 * @package Koowa\Component\Koowa\Controller\Toolbar
 */
class ComKoowaControllerToolbarActionbar extends KControllerToolbarActionbar
{
    /**
     * A list of Joomla standard toolbar buttons to correctly translate labels
     *
     * @var array
     */
    protected static $_default_buttons = array('save', 'apply', 'cancel', 'new', 'delete', 'publish', 'unpublish', 'export', 'back', 'options');

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
            'title' => ucfirst($this->getName()),
            'icon'  => $this->getName(),
        ));

        parent::_initialize($config);
    }

    /**
     * Load the language strings for toolbar button labels
     *
     * @param KControllerContextInterface $context
     */
    protected function _beforeRender(KControllerContextInterface $context)
    {
        JFactory::getLanguage()->load('joomla', JPATH_ADMINISTRATOR);
    }

    /**
     * Add default action commands and set the action bar title
     * .
     *
     * @param KControllerContextInterface $context A command context object
     */
    protected function _afterRead(KControllerContextInterface $context)
    {
        $controller = $this->getController();
        $translator = $this->getObject('translator');
        $name       = $translator->translate(strtolower($context->subject->getIdentifier()->name));

        if($controller->getModel()->getState()->isUnique()) {
            $title = $translator->translate('Edit {item_type}', array('item_type' => $name));
        } else {
            $title = $translator->translate('Create new {item_type}', array('item_type' => $name));
        }

        $this->getCommand('title')->title = $title;

        parent::_afterRead($context);
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
        $this->_commands['title'] = new KControllerToolbarCommand('title', array(
            'title' => $title,
            'icon'  => $icon
        ));
        return $this;
    }
    
    /**
     * If the method is called with one of the standard Joomla toolbar buttons translate the label correctly
     *
     * @param string $name   The command name
     * @param array  $config The command configuration options
     *
     * @return $this
     */
    public function addCommand($name, $config = array())
    {
        if (!isset($config['label']) && in_array($name, self::$_default_buttons)) {
            $config['label'] = 'JTOOLBAR_'.$name;
        }

        return parent::addCommand($name, $config);
    }

    /**
     * Publish Toolbar Command
     *
     * @param   KControllerToolbarCommand $command  A KControllerToolbarCommand object
     * @return  void
     */
    protected function _commandPublish(KControllerToolbarCommand $command)
    {
        $this->_commandEnable($command);
    }

    /**
     * Unpublish Toolbar Command
     *
     * @param   KControllerToolbarCommand $command  A KControllerToolbarCommand object
     * @return  void
     */
    protected function _commandUnpublish(KControllerToolbarCommand $command)
    {
        $this->_commandDisable($command);
    }

    /**
     * Disable Toolbar Command
     *
     * @param   KControllerToolbarCommand $command  A KControllerToolbarCommand object
     * @return  void
     */
    protected function _commandSave2new(KControllerToolbarCommand $command)
    {
        $command->label = 'JTOOLBAR_SAVE_AND_NEW';
        $command->icon = 'icon-32-save-new';
    
        $command->append(array(
            'attribs' => array(
                'data-action' => 'save2new'
            )
        ));
    }

    /**
     * Cancel Toolbar Command
     *
     * @param   KControllerToolbarCommand $command  A KControllerToolbarCommand object
     * @return  void
     */
    protected function _commandCancel(KControllerToolbarCommand $command)
    {
        $command->label = 'JTOOLBAR_CANCEL';
        $command->icon = 'icon-32-cancel';

        $command->append(array(
            'attribs' => array(
                'data-action' => 'cancel',
                'data-novalidate' => 'novalidate',
            )
        ));
    }

    /**
     * Options Toolbar Command
     *
     * @param   KControllerToolbarCommand $command  A KControllerToolbarCommand object
     * @return  void
     */
    protected function _commandOptions(KControllerToolbarCommand $command)
    {
        $option = $this->getIdentifier()->package;
        $icon   = 'options';
        
        if (version_compare(JVERSION, '3.0', '>='))
        {
        	$return = urlencode(base64_encode(JUri::getInstance()));
        	$link   = 'option=com_config&view=component&component=com_'.$option.'&path=&return='.$return;
        }
        else
        {
            JHtml::_('behavior.modal');

            $link = 'option=com_config&view=component&component=com_'.$option.'&path=&tmpl=component';

            $command->append(array(
                'attribs' => array(
                    'rel'   => "{handler: 'iframe', size: {x: 875, y: 550}, onClose: function() {}}",
                    'class' => array('modal')
                )
            ));
        }
        
        $command->icon = sprintf('icon-32-%s', $icon);
        // Need to do a JRoute call here, otherwise component is turned into option in the query string by our router
        $command->attribs['href'] = JRoute::_('index.php?'.$link, false);
    }
}

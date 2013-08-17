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
class ComKoowaControllerToolbarActionbar extends KControllerToolbarActionbar
{
    /**
     * A list of Joomla standard toolbar buttons to correctly translate labels
     *
     * @var array
     */
    protected static $_default_buttons = array('save', 'apply', 'cancel', 'new', 'delete', 'publish', 'unpublish', 'export', 'back', 'options');
    
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
     * Options Toolbar Command
     *
     * @param   KControllerToolbarCommand $command  A KControllerToolbarCommand object
     * @return  void
     */
    protected function _commandOptions(KControllerToolbarCommand $command)
    {
        $option = $this->getIdentifier()->package;
        $type   = 'modal';
        $icon   = 'options';
        
        if (version_compare(JVERSION, '3.0', '>='))
        {
        	$type   = 'link';
        	$return = urlencode(base64_encode(JUri::getInstance()));
        	$link   = 'index.php?option=com_config&view=component&component=com_'.$option.'&path=&return='.$return;
        }
        else {
            $link = 'index.php?option=com_config&view=component&component=com_'.$option.'&path=&tmpl=component';
        }
        
        $command->icon = sprintf('icon-32-%s', $icon);

        $command->append(array(
            'attribs' => array(
                'href' => JRoute::_($link)
            )
        ));

        if ($type === 'modal') {
        	$this->_commandModal($command);
        }
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
}

<?php
/**
 * @version   	$Id$
 * @package     Nooku_Components
 * @subpackage  Default
 * @copyright  	Copyright (C) 2007 - 2012 Johan Janssens. All rights reserved.
 * @license   	GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.nooku.org
 */

/**
 * Default Toolbar
 *
 * @author      Johan Janssens <johan@nooku.org>
 * @package     Nooku_Components
 * @subpackage  Default
 */
class ComDefaultControllerToolbarDefault extends KControllerToolbarDefault
{
    /**
     * A list of Joomla standard toolbar buttons to correctly translate labels
     * @var array
     */
    protected static $_default_buttons = array('save', 'apply', 'cancel', 'new', 'delete', 'publish', 'unpublish', 'export', 'back', 'options');
    
    /**
     * If the method is called with one of the standard Joomla toolbar buttons
     * translate the label correctly
     */
    public function addCommand($name, $config = array())
    {
        if (version_compare(JVERSION, '1.6', '>=') && !isset($config['label']) && in_array($name, self::$_default_buttons)) {
            $config['label'] = 'JTOOLBAR_'.$name;
        }

        return parent::addCommand($name, $config);
    }
    
    protected function _commandPublish(KControllerToolbarCommand $command)
    {
        return $this->_commandEnable($command);
    }
    
    protected function _commandUnpublish(KControllerToolbarCommand $command)
    {
        return $this->_commandDisable($command);
    }
    
    /**
     * Enable toolbar command
     *
     * @param   object  A KControllerToolbarCommand object
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
     * @param   object  A KControllerToolbarCommand object
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
     * Export toolbar command
     *
     * @param   object  A KControllerToolbarCommand object
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
        elseif (version_compare(JVERSION, '1.6', '<')) {
            $link = 'index.php?option=com_config&controller=component&component=com_'.$option.'&path=';
            $icon = 'config';
        } else {
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
     * @param   object  A KControllerToolbarCommand object
     * @return  void
     */
    protected function _commandModal(KControllerToolbarCommand $command)
    {
        $option = $this->getIdentifier()->package;

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
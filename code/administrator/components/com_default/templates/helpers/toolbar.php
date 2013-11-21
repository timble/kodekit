<?php
/**
 * @version     $Id: behavior.php 3364 2011-05-25 21:07:41Z johanjanssens $
 * @package     Nooku_Components
 * @subpackage  Default
 * @copyright   Copyright (C) 2007 - 2012 Johan Janssens. All rights reserved.
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.nooku.org
 */

/**
 * Template Toolbar Helper
 *
 * @author      Johan Janssens <johan@nooku.org>
 * @package     Nooku_Components
 * @subpackage  Default
 */
class ComDefaultTemplateHelperToolbar extends KTemplateHelperAbstract
{
	/**
     * Render the toolbar title
     *
     * @param   array   An optional array with configuration options
     * @return  string  Html
     */
    public function title($config = array())
    {
        $config = new KConfig($config);
        $config->append(array(
        	'toolbar' => null
        ));

        $title = $this->translate($config->toolbar->getTitle());

        if (version_compare(JVERSION, '3.0', 'ge'))
        {
            if (version_compare(JVERSION, '3.2', 'ge'))
            {
                // Strip the extension.
                $icons = explode(' ', $config->toolbar->getIcon());
                foreach ($icons as &$icon) {
                    $icon = preg_replace('#\.[^.]*$#', '', $icon);
                }

                $layout = new JLayoutFile('joomla.toolbar.title');
                $html = $layout->render(array('title' => $title, 'icon' => $icon));
            }
            else
            {
                // Strip the extension.
                $icons = explode(' ', $config->toolbar->getIcon());
                foreach ($icons as &$icon) {
                    $icon = 'icon-48-' . preg_replace('#\.[^.]*$#', '', $icon);
                }

                $html = '<div class="pagetitle ' . htmlspecialchars(implode(' ', $icons)) . '"><h2>' . $title . '</h2></div>';
            }

            $app = JFactory::getApplication();
            $app->JComponentTitle = $html;
            JFactory::getDocument()->setTitle($app->getCfg('sitename') . ' - ' . JText::_('JADMINISTRATION') . ' - ' . $title);

        	return '';
        }

        $html = '<div class="header pagetitle icon-48-'.$config->toolbar->getIcon().'">';

        if (version_compare(JVERSION,'1.6.0','ge')) {
			$html .= '<h2>'.$title.'</h2>';
        } else {
            $html .= $title;
        }

		$html .= '</div>';

        return $html;
    }

    /**
     * Render the toolbar
     *
     * @param   array   An optional array with configuration options
     * @return  string  Html
     */
    public function render($config = array())
    {
        $config = new KConfig($config);
        $config->append(array(
        	'toolbar' => null
        ));

        if (version_compare(JVERSION, '3.0', 'ge')) {
        	$html = '<div class="btn-toolbar toolbar-list" id="toolbar">';
        	$html .= '%s';
		    $html .= '</div>';
        }
        elseif (version_compare(JVERSION, '1.6.0', 'ge')) 
        {
		    $html  = '<div class="toolbar-list" id="toolbar-'.$config->toolbar->getName().'">';
		    $html .= '<ul>';
		    $html .= '%s';
		    $html .= '</ul>';
		    $html .= '<div class="clr"></div>';
		    $html .= '</div>';
        } 
        else 
        {
            $html  = '<div class="toolbar toolbar-list" id="toolbar-'.$config->toolbar->getName().'">';
            $html .= '<table class="toolbar">';
            $html .= '<tr>';
            $html .= '%s';
    		$html .= '</tr>';
    		$html .= '</table>';
		    $html .= '</div>';
        }

        $buttons = '';
	    foreach ($config->toolbar->getCommands() as $command)
	    {
            $name = $command->getName();

	        if(method_exists($this, $name)) {
                $buttons .= $this->$name(array('command' => $command));
            } else {
                $buttons .= $this->command(array('command' => $command));
            }
       	}

       	$html = sprintf($html, $buttons);
       	 
		return $html;
    }

    /**
     * Render a toolbar command
     *
     * @param   array   An optional array with configuration options
     * @return  string  Html
     */
    public function command($config = array())
    {
        $config = new KConfig($config);
        $config->append(array(
        	'command' => NULL
        ));

        $command = $config->command;

         //Add a toolbar class	
        $command->attribs->class->append(array('toolbar'));
        $command->attribs->append(array('href' => '#'));

        //Create the id
        $id = 'toolbar-'.$command->id;
        
        if (version_compare(JVERSION, '3.0', 'ge')) {
        	$command->attribs->class->append(array('btn', 'btn-small'));
			
        	$icon = str_replace('icon-32-', '', $command->icon);
        	if ($command->id === 'new' || $command->id === 'apply') {
        		$command->attribs->class->append(array('btn-success'));
        		$icon .= ' icon-white';
        	}

        	$command->attribs->class = implode(" ", KConfig::unbox($command->attribs->class));
        	
        	$html = '<div class="btn-group" id="'.$id.'">';
        	$html .= '<a '.KHelperArray::toString($command->attribs).'>';
        	$html .= '<i class="icon-'.$icon.'"></i> ';
        	$html .= $this->translate($command->label);
        	$html .= '</a>';
        	$html .= '</div>';
        	
        	return $html;
        }

		$command->attribs->class = implode(" ", KConfig::unbox($command->attribs->class));
		
		if (version_compare(JVERSION,'1.6.0','ge')) {
		    $html = '<li class="button" id="'.$id.'">';
		} else {
		    $html = '<td class="button" id="'.$id.'">';
		}
        
        $html .= '<a '.KHelperArray::toString($command->attribs).'>';
        $html .= '<span class="'.$command->icon.'" title="'.$this->translate($command->title).'"></span>';
       	$html .= $this->translate($command->label);
       	$html .= '</a>';
       	
        if (version_compare(JVERSION, '1.6.0', 'ge')) {
		    $html .= '</li>';
		} else {
		    $html .= '</td>';
		}

    	return $html;
    }

	/**
     * Render a separator
     *
     * @param   array   An optional array with configuration options
     * @return  string  Html
     */
    public function separator($config = array())
    {
        $config = new KConfig($config);
        $config->append(array(
        	'command' => NULL
        ));
        
    	if (version_compare(JVERSION,'3.0','ge')) {
            $html = '<div class="btn-group"></div>';
        } elseif (version_compare(JVERSION,'1.6.0','ge')) {
            $html = '<li class="divider"></li>';
        } else {
            $html = '<td class="divider"></td>';
        }

    	return $html;
    }

	/**
     * Render a modal button
     *
     * @param   array   An optional array with configuration options
     * @return  string  Html
     */
    public function modal($config = array())
    {
        $config = new KConfig($config);
        $config->append(array(
        	'command' => NULL
        ));

        $html  = $this->getTemplate()->renderHelper('behavior.modal');
        $html .= $this->command($config);

    	return $html;
    }
    
    public function options($config = array())
    {
        return $this->modal($config);
    }
}

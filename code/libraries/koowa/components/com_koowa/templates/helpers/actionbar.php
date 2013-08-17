<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright	Copyright (C) 2007 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/koowa for the canonical source repository
 */

/**
 * Action bar Template Helper
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Koowa\Component\Koowa
 */
class ComKoowaTemplateHelperActionbar extends KTemplateHelperAbstract
{
	/**
     * Render the action bar title
     *
     * @param   array   $config An optional array with configuration options
     * @return  string  Html
     */
    public function title($config = array())
    {
        $config = new KConfig($config);
        $config->append(array(
        	'actionbar' => null
        ));

        $title = $this->translate($config->actionbar->getTitle());
        
        if ($this->_useBootstrap())
        {
            // Strip the extension.
            $icons = explode(' ', $config->actionbar->getIcon());
            foreach ($icons as &$icon) {
                $icon = 'icon-48-' . preg_replace('#\.[^.]*$#', '', $icon);
            }

            $html = '<div class="pagetitle ' . htmlspecialchars(implode(' ', $icons)) . '"><h2>' . $title . '</h2></div>';

            $app = JFactory::getApplication();
            $app->JComponentTitle = $html;
            JFactory::getDocument()->setTitle($app->getCfg('sitename') . ' - ' . JText::_('JADMINISTRATION') . ' - ' . $title);

        	return '';
        }

        $html = '<div class="header pagetitle icon-48-'.$config->actionbar->getIcon().'">';
        $html .= '<h2>'.$title.'</h2>';
		$html .= '</div>';

        return $html;
    }

    /**
     * Render the action bar commands
     *
     * @param   array   $config An optional array with configuration options
     * @return  string  Html
     */
    public function commands($config = array())
    {
        $config = new KConfig($config);
        $config->append(array(
        	'actionbar' => null
        ));

        if ($this->_useBootstrap())
        {
        	$html = '<div class="btn-toolbar toolbar-list" id="toolbar">';
        	$html .= '%s';
		    $html .= '</div>';
        }
        else
        {
		    $html  = '<div class="toolbar-list" id="toolbar-'.$config->actionbar->getName().'">';
		    $html .= '<ul>';
		    $html .= '%s';
		    $html .= '</ul>';
		    $html .= '<div class="clr"></div>';
		    $html .= '</div>';
        }

        $buttons = '';
	    foreach ($config->actionbar->getCommands() as $command)
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
     * Render a action bar command
     *
     * @param   array|KConfig   $config An optional array with configuration options
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

        //Create the href
        $command->attribs->append(array('href' => '#'));
        if(!empty($command->href)) {
            $command->attribs['href'] = $this->getTemplate()->getView()->createRoute($command->href);
        }

        //Create the id
        $id = 'toolbar-'.$command->id;
        
        if ($this->_useBootstrap())
        {
        	$command->attribs->class->append(array('btn', 'btn-small'));
			
        	$icon = $this->_getIconClass($command->icon);
        	if ($command->id === 'new' || $command->id === 'apply') {
        		$command->attribs->class->append(array('btn-success'));
        		$icon .= ' icon-white';
        	}

            $attribs = clone $command->attribs;
            $attribs->class = implode(" ", KConfig::unbox($attribs->class));
        	
        	$html = '<div class="btn-group" id="'.$id.'">';
        	$html .= '<a '.KHelperArray::toString($attribs).'>';
        	$html .= '<i class="'.$icon.'"></i> ';
        	$html .= $this->translate($command->label);
        	$html .= '</a>';
        	$html .= '</div>';
        	
        	return $html;
        }


        $attribs = clone $command->attribs;
        $attribs->class = implode(" ", KConfig::unbox($attribs->class));

        $html = '<li class="button" id="'.$id.'">';
        
        $html .= '<a '.KHelperArray::toString($attribs).'>';
        $html .= '<span class="'.$command->icon.'" title="'.$this->translate($command->title).'"></span>';
       	$html .= $this->translate($command->label);
       	$html .= '</a>';

        $html .= '</li>';

    	return $html;
    }

	/**
     * Render a separator
     *
     * @param   array   $config An optional array with configuration options
     * @return  string  Html
     */
    public function separator($config = array())
    {
        $config = new KConfig($config);
        $config->append(array(
        	'command' => NULL
        ));
        
    	if ($this->_useBootstrap()) {
            $html = '<div class="btn-group"></div>';
        } else {
            $html = '<li class="divider"></li>';
        }

    	return $html;
    }

	/**
     * Render a modal button
     *
     * @param   array   $config An optional array with configuration options
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

    /**
     * Render an options button
     *
     * @param array|KConfig $config
     * @return string
     */
    public function options($config = array())
    {
        return $this->modal($config);
    }

    /**
     * Decides if the renderers should use Bootstrap markup or not
     *
     * @return bool
     */
    protected function _useBootstrap()
    {
        return version_compare(JVERSION, '3.0', '>=') || JFactory::getApplication()->isSite();
    }

    /**
     * Converts Joomla 3.0+ custom icons back to Glyphicons ones used in Joomla 2.5
     *
     * @param  string $icon Action bar icon
     * @return string Icon class
     */
    protected function _getIconClass($icon)
    {
        static $map = array(
            'icon-save'   => 'icon-ok',
            'icon-cancel' => 'icon-remove-sign',
            'icon-apply'  => 'icon-edit'
        );

        if (version_compare(JVERSION, '3.0', '>=') || JFactory::getApplication()->isSite()) {
            $icon = str_replace('icon-32-', 'icon-', $icon);
        }

        if (version_compare(JVERSION, '3.0', '<') && array_key_exists($icon, $map)) {
            $icon = $map[$icon];
        }

        return $icon;
    }
}

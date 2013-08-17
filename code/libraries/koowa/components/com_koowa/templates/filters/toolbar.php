<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright	Copyright (C) 2007 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/koowa for the canonical source repository
 */


/**
 * Toolbar Template Filter
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Koowa\Component\Koowa
 */
class ComKoowaTemplateFilterToolbar extends KTemplateFilterAbstract implements KTemplateFilterWrite
{
    /**
     * Toolbars to render such as actionbar, menubar, ...
     *
     * @var array
     */
    protected $_toolbars = array();

    /**
     * Get the list of toolbars to be rendered
     *
     * @return array
     */
    public function getToolbars()
    {
        return $this->_toolbars;
    }

    /**
     * Set the toolbars to render
     *
     * @param array $toolbars
     * @return $this
     */
    public function setToolbars(array $toolbars)
    {
        $this->_toolbars = $toolbars;
        return $this;
    }

    /**
     * Returns the menu bar instance
     *
     * @return KControllerToolbarInterface
     */
    public function getToolbar($type = 'actionbar')
    {
        return isset($this->_toolbars[$type]) ? $this->_toolbars[$type] : null;
    }

    /**
     * Sets the menu bar instance
     *
     * @param KControllerToolbarInterface $menubar
     * @return ComKoowaTemplateFilterToolbar
     */
    public function setToolbar(KControllerToolbarInterface $toolbar, $type = 'actionbar')
    {
        $this->_toolbars[$type] = $toolbar;
        return $this;
    }

    /**
     * Replace/push the toolbars
     *
     * @param string $text Block of text to parse
     * @return ComKoowaTemplateFilterToolbar
     */
    public function write(&$text)
    {
        if ($toolbar = $this->getToolbar('actionbar')) {
            $this->_renderActionbar($text, $toolbar);
        }

        if ($toolbar = $this->getToolbar('menubar')) {
            $this->_renderMenubar($text, $toolbar);
        }

        return $this;
    }

    /**
     * Renders the action bar
     *
     * @param string $text Block of text to parse
     * @param KControllerToolbarInterface $toolbar
     * @return void
     */
    protected function _renderActionbar(&$text, KControllerToolbarInterface $toolbar)
    {
        // Load the language strings for toolbar button labels
        JFactory::getLanguage()->load('joomla', JPATH_ADMINISTRATOR);

        $actionbar = $this->getTemplate()->getHelper('actionbar');

        $commands = $actionbar->commands(array('actionbar' => $toolbar));
        $title    = $actionbar->title(array('actionbar' => $toolbar));

        if (!JFactory::getApplication()->isAdmin())
        {
            $needle = '<ktml:actionbar>';

            if (strpos($text, $needle) !== false) {
                $text = str_replace($needle, $commands, $text);
            }

            $needle = '<ktml:titlebar>';

            if (strpos($text, $needle) !== false) {
                $text = str_replace($needle, $title, $text);
            }
        }
        else
        {
            if(!empty($title)) {
                JFactory::getDocument()->setBuffer($title  , 'modules', 'title');
            }

            if(!empty($commands)) {
                JFactory::getDocument()->setBuffer($commands, 'modules', 'toolbar');
            }
        }
    }

    /**
     * Renders the menu bar
     *
     * @param string $text Block of text to parse
     * @param KControllerToolbarInterface $toolbar
     * @return void
     */
    protected function _renderMenubar(&$text, KControllerToolbarInterface $toolbar)
    {
        $menubar  = $this->getTemplate()->getHelper('menubar');
        $commands = $menubar->commands(array('menubar' => $toolbar));

        if (!JFactory::getApplication()->isAdmin())
        {
            $needle = '<ktml:menubar>';

            if (strpos($text, $needle) !== false) {
                $text = str_replace($needle, $commands, $text);
            }
        }
        else
        {
            if(!empty($commands)) {
                JFactory::getDocument()->setBuffer($commands, 'modules', 'submenu');
            }
        }
    }
}
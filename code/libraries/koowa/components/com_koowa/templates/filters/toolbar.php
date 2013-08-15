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
     * Toolbars to render such as toolbar, menubar, title
     * @var array
     */
    protected $_toolbars = array();

    /**
     * Toolbar
     *
     * @var KControllerToolbarInterface
     */
    protected $_toolbar;

    /**
     * Toolbar
     *
     * @var KControllerToolbarInterface
     */
    protected $_menubar;


    /**
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
     * Returns the menubar instance
     *
     * @return KControllerToolbarInterface
     */
    public function getMenubar()
    {
        return $this->_menubar;
    }

    /**
     * Sets the menubar instance
     *
     * @param KControllerToolbarInterface $menubar
     * @return $this
     */
    public function setMenubar(KControllerToolbarInterface $menubar)
    {
        $this->_menubar = $menubar;

        return $this;
    }

    /**
     * Returns the toolbar instance
     *
     * @return KControllerToolbarInterface
     */
    public function getToolbar()
    {
        return $this->_toolbar;
    }

    /**
     * Sets the toolbar instance
     *
     * @param KControllerToolbarInterface $toolbar
     * @return $this
     */
    public function setToolbar(KControllerToolbarInterface $toolbar)
    {
        $this->_toolbar = $toolbar;

        return $this;
    }

    /**
     * Replace/push toolbar
     *
     * @param string $text Block of text to parse
     * @return $this
     */
    public function write(&$text)
    {
        $toolbars = $this->getToolbars();

        if (in_array('toolbar', $toolbars) && $this->getToolbar()) {
            $this->_renderToolbar($text);
        }

        if (in_array('menubar', $toolbars) && $this->getMenubar()) {
            $this->_renderMenubar($text);
        }

        if (in_array('title', $toolbars) && $this->getToolbar()) {
            $this->_renderTitle($text);
        }

        return $this;
    }

    /**
     * Renders the title
     *
     * @param string $text Block of text to parse
     * @return void
     */
    protected function _renderTitle(&$text)
    {
        $title = $this->getTemplate()->getHelper('toolbar')->title(array(
            'toolbar' => $this->getToolbar()
        ));

        if (JFactory::getApplication()->isAdmin()) {
            JFactory::getDocument()->setBuffer($title, 'modules', 'title');
        }
        else
        {
            $needle = '<ktml:title>';

            if ($this->getToolbar() && strpos($text, $needle) !== false) {
                $text = str_replace($needle, $title, $text);
            }
        }
    }

    /**
     * Renders the toolbar
     *
     * @param string $text Block of text to parse
     * @return void
     */
    protected function _renderToolbar(&$text)
    {
        // Load the language strings for toolbar button labels
        JFactory::getLanguage()->load('joomla', JPATH_ADMINISTRATOR);

        $toolbar = $this->getTemplate()->getHelper('toolbar')->render(array(
            'toolbar' => $this->getToolbar()
        ));

        if (JFactory::getApplication()->isAdmin()) {
            JFactory::getDocument()->setBuffer($toolbar, 'modules', 'toolbar');
        }
        else
        {
            $needle = '<ktml:toolbar>';

            if ($this->getToolbar() && strpos($text, $needle) !== false) {
                $text = str_replace($needle, $toolbar, $text);
            }
        }
    }

    /**
     * Renders the menubar
     *
     * @param string $text Block of text to parse
     * @return void
     */
    protected function _renderMenubar(&$text)
    {
        if ($this->getMenubar())
        {
            $menubar = $this->getTemplate()->getHelper('menubar')->render(array(
                'menubar' => $this->getMenubar()
            ));

            if (JFactory::getApplication()->isAdmin()) {
                JFactory::getDocument()->setBuffer($menubar, 'modules', 'submenu');
            }
            else
            {
                $needle = '<ktml:menubar>';

                if (strpos($text, $needle) !== false) {
                    $text = str_replace($needle, $menubar, $text);
                }
            }
        }

    }
}
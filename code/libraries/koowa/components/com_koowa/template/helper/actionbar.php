<?php
/**
 * Nooku Framework - http://nooku.org/framework
 *
 * @copyright   Copyright (C) 2007 - 2014 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/nooku/nooku-framework for the canonical source repository
 */

/**
 * Action bar Template Helper
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Koowa\Component\Koowa\Template\Helper
 */
class ComKoowaTemplateHelperActionbar extends KTemplateHelperActionbar
{
    /**
     * Render the action bar commands
     *
     * @param   array   $config An optional array with configuration options
     * @return  string  Html
     */
    public function render($config = array())
    {
        // Load the language strings for toolbar button labels
        JFactory::getLanguage()->load('joomla', JPATH_ADMINISTRATOR);

        return parent::render($config);
    }

    /**
     * Render the action bar title
     *
     * @param   array   $config An optional array with configuration options
     * @return  string  Html
     */
    public function title($config = array())
    {
        $config = new KObjectConfigJson($config);
        $config->append(array(
            'command' => NULL,
        ));

        $title = $this->getObject('translator')->translate($config->command->title);
        $icon  = $config->command->icon;
        $html  = '';

        if (!empty($title))
        {
            if (JFactory::getApplication()->isAdmin() && version_compare(JVERSION, '3.2', 'ge'))
            {
                $layout = new JLayoutFile('joomla.toolbar.title');
                $html = $layout->render(array('title' => $title, 'icon' => $icon));
            }
            elseif ($this->_useBootstrap())
            {
                // Strip the extension.
                $icons = explode(' ', $icon);
                foreach ($icons as &$icon) {
                    $icon = 'pagetitle--' . preg_replace('#\.[^.]*$#', '', $icon);
                }

                $html = '<div class="pagetitle ' . htmlspecialchars(implode(' ', $icons)) . '"><h2>' . $title . '</h2></div>';
            }
            else
            {
                $html = '<div class="header pagetitle icon-48-'.$icon.'">';
                $html .= '<h2>'.$title.'</h2>';
                $html .= '</div>';
            }

            if (JFactory::getApplication()->isAdmin())
            {
                $app = JFactory::getApplication();
                $app->JComponentTitle = $html;

                $html = '';

                JFactory::getDocument()->setTitle($app->getCfg('sitename') . ' - ' . JText::_('JADMINISTRATION') . ' - ' . $title);
            }
        }

        return $html;
    }

    /**
     * Render an options button
     *
     * @param array|KObjectConfig $config
     * @return string
     */
    public function options($config = array())
    {
        return $this->dialog($config);
    }

    /**
     * Render a modal button
     *
     * @param   array   $config An optional array with configuration options
     * @return  string  Html
     */
    public function dialog($config = array())
    {
        JHtml::_('behavior.modal');

        return parent::dialog($config);
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
     * Decides if Bootstrap buttons should use icons
     *
     * @return bool
     */
    protected function _useIcons()
    {
        return JFactory::getApplication()->isAdmin();
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

        if (version_compare(JVERSION, '3.0', '>=') || $this->getIdentifier()->domain == 'site') {
            $icon = str_replace('icon-32-', 'icon-', $icon);
        }

        if (version_compare(JVERSION, '3.0', '<') && array_key_exists($icon, $map)) {
            $icon = $map[$icon];
        }

        return $icon;
    }
}

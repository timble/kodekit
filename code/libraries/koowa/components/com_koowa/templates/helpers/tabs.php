<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright	Copyright (C) 2007 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/koowa for the canonical source repository
 */

/**
 * Tabs Template Helper
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Koowa\Component\Koowa
 */
class ComKoowaTemplateHelperTabs extends KTemplateHelperAbstract
{
    /**
     * Creates a pane and creates the javascript object for it
     *
     * @param   array   $config An optional array with configuration options
     * @return  string  Html
     */
    public function startPane($config = array())
    {
        $config = new KConfig($config);
        $config->append(array(
            'id'      => 'tabs',
            'options' => array()
        ));

        return JHtml::_('tabs.start', $config->id, KConfig::unbox($config->options));
    }

    /**
     * Ends the pane
     *
     * @param   array   $config An optional array with configuration options
     * @return  string  Html
     */
    public function endPane($config = array())
    {
        return JHtml::_('tabs.end');
    }

    /**
     * Creates a tab panel with title and starts that panel
     *
     * @param   array   $config An optional array with configuration options
     * @return  string  Html
     */
    public function startPanel( $config = array())
    {
        $config = new KConfig($config);
        $config->append(array(
            'title'     => '',
            'class'     => '',
            'translate' => true
        ));

        $title = $config->translate ? $this->translate($config->title) : $config->title;

        $class = KConfig::unbox($config->class);

        if (is_array($class)) {
            $class = implode(' ', $class);
        }

        return JHtml::_('tabs.panel', $title, $class);
    }

    /**
     * Ends a tab page
     *
     * @param   array   $config An optional array with configuration options
     *
     * @return  string  Html
     */
    public function endPanel($config = array())
    {
        return '';
    }
}

<?php
/**
 * @version     $Id: behavior.php 1051 2009-07-13 22:08:57Z Johan $
 * @package     Koowa_Template
 * @subpackage  Helper
 * @copyright   Copyright (C) 2007 - 2012 Johan Janssens. All rights reserved.
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.nooku.org
 */

/**
 * Template Tabs Behavior Helper
 *
 * @author      Johan Janssens <johan@nooku.org>
 * @package     Koowa_Template
 * @subpackage  Helper
 * @uses        KArrayHelper
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
<?php
/**
 * Nooku Framework - http://nooku.org/framework
 *
 * @copyright   Copyright (C) 2007 - 2014 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/nooku/nooku-framework for the canonical source repository
 */

/**
 * Accordion Template Helper
 *
 * @author  Stian Didriksen <https://github.com/stipsan>
 * @package Koowa\Component\Koowa\Template\Helper
 */
class ComKoowaTemplateHelperAccordion extends KTemplateHelperAbstract
{
    /**
     * Creates a pane and creates the javascript object for it
     *
     * @param   array|KObjectConfig $config An optional array with configuration options
     * @return  string  Html
     */
    public function startPane($config = array())
    {
        $config = new KObjectConfigJson($config);

        $config->append(array(
            'id'    => 'sliders',
            'options'   => array(
                'duration'      => 300,
                'opacity'       => false,
                'alwaysHide'    => true,
                'scroll'        => false
            )
        ));

        return JHtml::_('sliders.start', $config->id, KObjectConfig::unbox($config->options));
    }

    /**
     * Ends the pane
     *
     * @param   array|KObjectConfig $config An optional array with configuration options
     * @return  string  Html
     */
    public function endPane($config = array())
    {
        return JHtml::_('sliders.end');
    }

    /**
     * Creates a tab panel with title and starts that panel
     *
     * @param   array|KObjectConfig $config An optional array with configuration options
     * @return  string Html
     */
    public function startPanel($config = array())
    {
        $translator = $this->getObject('translator');

        $config = new KObjectConfigJson($config);
        $config->append(array(
            'title'     => $translator->translate('Slide'),
            'id'        => '',
            'translate' => true
        ));

        $title = $config->translate ? $translator->translate($config->title) : $config->title;

        return JHtml::_('sliders.panel', $title, KObjectConfig::unbox($config->attribs));
    }

    /**
     * Ends a tab page
     *
     * @param   array|KObjectConfig $config An optional array with configuration options
     * @return  string  Html
     */
    public function endPanel($config = array())
    {
        return '';
    }
}

<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright	Copyright (C) 2007 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/koowa for the canonical source repository
 */

/**
 * Page Html View
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Koowa\Component\Koowa
 */
class ComKoowaViewPageHtml extends ComKoowaViewHtml
{
    /**
     * Initializes the options for the object
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param   KObjectConfig $config Configuration options.
     * @return  void
     */
    protected function _initialize(KObjectConfig $config)
    {
        $config->append(array(
            'template_filters'	=> array('document', 'module', 'style', 'link', 'meta', 'script', 'title', 'message'),
        ));

        parent::_initialize($config);
    }

    protected function _fetchData(KViewContext $context)
    {
        //Set the language information
        $language = JFactory::getApplication()->getCfg('language');

        $context->data->language  = $language ? $language : 'en-GB';
        $context->data->direction = JFactory::getLanguage()->isRTL() ? 'rtl' : 'ltr';

        parent::_fetchData($context);
    }
}

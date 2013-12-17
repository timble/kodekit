<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright	Copyright (C) 2007 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/koowa for the canonical source repository
 */


/**
 * Script Template Filter
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Koowa\Component\Koowa
 */
class ComKoowaTemplateFilterLink extends KTemplateFilterLink
{
    /**
     * Render script information
     *
     * @param string    $link    The script information
     * @param array     $attribs Associative array of attributes
     * @return string
     */
    protected function _renderScript($link, $attribs = array())
    {
        if($this->getObject('request')->isAjax()) {
            return parent::_renderLink($link, $attribs);
        }

        $relType  = 'rel';
        $relValue = $attribs['rel'];
        unset($attribs['rel']);

        JFactory::getDocument()->addHeadLink($link, $relValue, $relType, $attribs);

        return '';
    }
}

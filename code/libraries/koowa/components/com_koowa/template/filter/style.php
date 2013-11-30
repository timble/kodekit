<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright	Copyright (C) 2007 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/koowa for the canonical source repository
 */


/**
 * Style Template Filter
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Koowa\Component\Koowa
 */
class ComKoowaTemplateFilterStyle extends KTemplateFilterStyle
{
    /**
     * An array of MD5 hashes for loaded style strings
     */
    protected $_loaded = array();

    /**
     * Render style information
     *
     * First checks if the style has been loaded already
     * Note that for links the check is done in JDocument so no need to repeat here.
     *
     * @param string    $style  The style information
     * @param boolean   $link   True, if the style information is a URL
     * @param array     $attribs Associative array of attributes
     * @return string
     */
    protected function _renderStyle($style, $link, $attribs = array())
    {
        if(KRequest::type() == 'AJAX') {
            return parent::_renderStyle($style, $link, $attribs);
        }

        $document = JFactory::getDocument();

        if($link) {
            $document->addStyleSheet($style, 'text/css', null, $attribs);
        }
        else
        {
            $hash = md5($style.serialize($attribs));

            if (!isset($this->_loaded[$hash])) {
                $document->addStyleDeclaration($style);

                $this->_loaded[$hash] = true;
            }
        }

        return '';
    }
}

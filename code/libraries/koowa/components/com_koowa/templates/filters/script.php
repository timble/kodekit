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
class ComKoowaTemplateFilterScript extends KTemplateFilterScript
{
    /**
     * Render script information
     *
     * @param string    The script information
     * @param boolean   True, if the script information is a URL.
     * @param array     Associative array of attributes
     * @return string
     */
    protected function _renderScript($script, $link, $attribs = array())
    {
        if(KRequest::type() == 'AJAX') {
            return parent::_renderScript($script, $link, $attribs);
        }

        $document = JFactory::getDocument();

        if($link) {
            $document->addScript($script, 'text/javascript');
        } else {
            $document->addScriptDeclaration($script);
        }
    }
}

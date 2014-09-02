<?php
/**
 * Nooku Framework - http://nooku.org/framework
 *
 * @copyright   Copyright (C) 2007 - 2014 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/nooku/nooku-framework for the canonical source repository
 */

/**
 * Script Template Filter
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Koowa\Component\Koowa\Template\Filter
 */
class ComKoowaTemplateFilterScript extends KTemplateFilterScript
{
    /**
     * An array of MD5 hashes for loaded script strings
     */
    protected $_loaded = array();

    /**
     * Find any virtual tags and render them
     *
     * This function will pre-pend the tags to the content
     *
     * @param string $text  The text to parse
     */
    public function filter(&$text)
    {
        $scripts = $this->_parseTags($text);

        if($this->getTemplate()->getParameters()->layout == 'koowa') {
            $text = str_replace('<ktml:script>', $scripts, $text);
        } else  {
            $text = $scripts.$text;
        }
    }

    /**
     * Render the tag
     *
     * @param   array   $attribs Associative array of attributes
     * @param   string  $content The tag content
     * @return string
     */
    protected function _renderTag($attribs = array(), $content = null)
    {
        if($this->getTemplate()->getParameters()->layout !== 'koowa')
        {
            $link      = isset($attribs['src']) ? $attribs['src'] : false;
            $condition = isset($attribs['condition']) ? $attribs['condition'] : false;

            if(!$link)
            {
                $script = trim($content);
                $hash   = md5($script.serialize($attribs));

                if (!isset($this->_loaded[$hash]))
                {
                    JFactory::getDocument()->addScriptDeclaration($script);
                    $this->_loaded[$hash] = true;
                }
            }
            else
            {
                if($condition)
                {
                    $script = parent::_renderTag($attribs, $content);
                    JFactory::getDocument()->addCustomTag($script);
                }
                else
                {
                    unset($attribs['src']);
                    unset($attribs['condition']);

                    JFactory::getDocument()->addScript($link, 'text/javascript');
                }
            }
        }
        else return parent::_renderTag($attribs, $content);
    }
}

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
class ComKoowaTemplateFilterMeta extends KTemplateFilterMeta
{
    /**
     * Find any virtual tags and render them
     *
     * This function will pre-pend the tags to the content
     *
     * @param string $text  The text to parse
     */
    public function filter(&$text)
    {
        $meta    = $this->_parseTags($text);

        if($this->getTemplate()->getParameters()->layout == 'koowa') {
            $text = str_replace('<ktml:meta>', $meta, $text);
        } else  {
            $text = $meta.$text;
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
            $meta = parent::_renderTag($attribs, $content);
            JFactory::getDocument()->addCustomTag($meta);
        }
        else return parent::_renderTag($attribs, $content);
    }
}

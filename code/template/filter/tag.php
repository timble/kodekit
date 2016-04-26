<?php
/**
 * Kodekit - http://timble.net/kodekit
 *
 * @copyright   Copyright (C) 2007 - 2016 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license     MPL v2.0 <https://www.mozilla.org/en-US/MPL/2.0>
 * @link        https://github.com/timble/kodekit for the canonical source repository
 */

namespace Kodekit\Library;

/**
 * Abstract Tag Template Filter
 *
 * Filter to parse tags
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Kodekit\Library\Template\Filter
 */
abstract class TemplateFilterTag extends TemplateFilterAbstract
{
    /**
     * Initializes the options for the object
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param  ObjectConfig $config  An optional ObjectConfig object with configuration options
     * @return void
     */
    protected function _initialize(ObjectConfig $config)
    {
        $config->append(array(
            'priority' => self::PRIORITY_LOW,
        ));

        parent::_initialize($config);
    }

    /**
     * Find any virtual tags and render them
     *
     * This function will pre-pend the tags to the content
     *
     * @param string $text  The text to parse
     * @param TemplateInterface $template A template object.
     * @return void
     */
    public function filter(&$text, TemplateInterface $template)
    {
        //Parse the tags
        $tags = $this->_parseTags($text);

        //Prepend the tags again to the text
        $text = $tags.$text;
    }

    /**
     * Parse the text for the tags
     *
     * @param string $text  The text to parse
     * @return string
     */
    abstract protected function _parseTags(&$text);

    /**
     * Render the tag
     *
     * @param   array   $attribs Associative array of attributes
     * @param   string  $content The element content
     * @return string
     */
    abstract protected function _renderTag($attribs = array(), $content = null);
}
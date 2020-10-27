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
 * Template Filter Interface
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Kodekit\Library\Template\Filter
 */
interface TemplateFilterInterface extends ObjectHandlable
{
    /**
     * Priority levels
     */
    const PRIORITY_HIGHEST = 1;
    const PRIORITY_HIGH    = 2;
    const PRIORITY_NORMAL  = 3;
    const PRIORITY_LOW     = 4;
    const PRIORITY_LOWEST  = 5;

    /**
     * Filter the text
     *
     * @param string $text  The text to parse
     * @param TemplateInterface $template A template object.
     * @return void
     */
    public function filter(&$text, TemplateInterface $template);

    /**
     * Get the priority of a behavior
     *
     * @return  integer The command priority
     */
    public function getPriority();

    /**
     * Method to extract key/value pairs out of a string with xml style attributes
     *
     * @param   string  String containing xml style attributes
     * @return  array   Key/Value pairs for the attributes
     */
    public function parseAttributes( $string );

    /**
     * Build an HTML element
     *
     * @param string $tag HTML tag name
     * @param array  $attributes Key/Value pairs for the attributes
     * @param string|array|callable $children Child elements, not applicable for self-closing tags
     * @return string
     *
     * Example:
     * ```php
     * echo $this->buildElement('a', ['href' => 'https://example.com/'], 'example link');
     * // returns '<a href="https://example.com/">example link</a>
     *
     * echo $this->buildElement('meta', ['name' => 'foo', 'content' => 'bar']);
     * // returns '<meta name="foo" content="bar" />
     *
     * ```
     */
    public function buildElement($tag, $attributes = [], $children = '');

    /**
     * Method to build a string with xml style attributes from  an array of key/value pairs
     *
     * @param   mixed   $array The array of Key/Value pairs for the attributes
     * @return  string  String containing xml style attributes
     */
    public function buildAttributes($array);
}

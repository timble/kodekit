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
 * Abstract Template Filter
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Kodekit\Library\Template\Filter
 */
abstract class TemplateFilterAbstract extends ObjectAbstract implements TemplateFilterInterface
{
    /**
     * The filter priority
     *
     * @var integer
     */
    protected $_priority;

    /**
     * Constructor.
     *
     * @param ObjectConfig $config An optional ObjectConfig object with configuration options
     */
    public function __construct(ObjectConfig $config)
    {
        parent::__construct($config);

        $this->_priority = $config->priority;
    }

    /**
     * Initializes the options for the object
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param  ObjectConfig $config An optional ObjectConfig object with configuration options
     */
    protected function _initialize(ObjectConfig $config)
    {
        $config->append(array(
            'priority' => self::PRIORITY_NORMAL
        ));

        parent::_initialize($config);
    }

    /**
     * Get the priority of a behavior
     *
     * @return  integer The command priority
     */
    public function getPriority()
    {
        return $this->_priority;
    }

    /**
     * Method to extract name/value pairs out of a string with xml style attributes
     *
     * @param   string  $string String containing xml style attributes
     * @return  array   name/value pairs for the attributes
     */
    public function parseAttributes($string)
    {
        $result = array();

        if (!empty($string))
        {
            $pattern = '#(?(DEFINE)
                (?<name>[a-zA-Z][a-zA-Z0-9-_:]*)
                (?<value_double>"[^"]+")
                (?<value_none>[^\s>]+)
                (?<value>((?&value_double)|(?&value_none)))
            )
            (?<n>(?&name))[\s]*(=[\s]*(?<v>(?&value)))?#xs';

            if (preg_match_all($pattern, $string, $matches, PREG_SET_ORDER))
            {
                foreach ($matches as $match)
                {
                    if (!empty($match['n'])) {
                        $result[$match['n']] = isset($match['v']) ? trim($match['v'], '\'"') : '';
                    }
                }
            }
        }

        return $result;
    }

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
    public function buildElement($tag, $attributes = [], $children = '')
    {
        static $self_closing_tags = [
            'area', 'base', 'br', 'col', 'embed', 'hr', 'img', 'input', 'link',
            'meta', 'param', 'source', 'track', 'wbr',
        ];

        $attribs = $this->buildAttributes($attributes);
        $attribs = $attribs ? ' '.$attribs : '';
        $tag     = strtolower($tag);

        if (in_array($tag, $self_closing_tags)) {
            return "<$tag$attribs>";
        } else if (strpos($tag, 'ktml:') === 0 && !$children) {
            return "<$tag$attribs />";
        } else {
            if (!is_scalar($children) && is_callable($children)) {
                $children = $children($tag, $attributes);
            }

            if (is_array($children)) {
                $children = implode("\n", $children);
            }

            return "<$tag$attribs>$children</$tag>";
        }
    }

    /**
     * Method to build a string with xml style attributes from  an array of key/value pairs
     *
     * @param   mixed   $array The array of Key/Value pairs for the attributes
     * @return  string  String containing xml style attributes
     */
    public function buildAttributes($array)
    {
        $output = array();

        if ($array instanceof ObjectConfig) {
            $array = ObjectConfig::unbox($array);
        }

        if (is_array($array))
        {
            foreach ($array as $key => $item)
            {
                if(is_array($item))
                {
                    if(empty($item)) {
                        continue;
                    }

                    $item = implode(' ', $item);
                }

                if (is_bool($item))
                {
                    if ($item === false) continue;
                    $item = $key;
                }

                $output[] = $key . '="' . str_replace('"', '&quot;', $item) . '"';
            }
        }

        return implode(' ', $output);
    }
}

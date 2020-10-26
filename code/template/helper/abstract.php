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
 * Template Helper
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Kodekit\Library\Template\Helper
 */
abstract class TemplateHelperAbstract extends ObjectAbstract implements TemplateHelperInterface
{
    /**
     * Template object
     *
     * @var	object
     */
    private $__template;

    /**
     * Constructor
     *
     * @throws \UnexpectedValueException    If no 'template' config option was passed
     * @throws \InvalidArgumentException    If the model config option does not implement TemplateInterface
     */
    public function __construct(ObjectConfig $config)
    {
        parent::__construct($config);

        $this->setTemplate($config->template);
    }

    /**
     * Initializes the options for the object
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param  ObjectConfig $config An optional ObjectConfig object with configuration options
     * @return void
     */
    protected function _initialize(ObjectConfig $config)
    {
        $config->append(array(
            'template' => 'default',
        ));

        parent::_initialize($config);
    }

    /**
     * Gets the template object
     *
     * @return  TemplateInterface	The template object
     */
    public function getTemplate()
    {
        if(!$this->__template instanceof TemplateInterface)
        {
            $this->__template = $this->getObject($this->__template);

            if(!$this->__template instanceof TemplateInterface)
            {
                throw new \UnexpectedValueException(
                    'Template: '.get_class($this->_model).' does not implement TemplateInterface'
                );
            }
        }

        return $this->__template;
    }

    /**
     * Sets the template object
     *
     * @param TemplateInterface $template
     * @return $this
     */
    public function setTemplate($template)
    {
        if(!$template instanceof TemplateInterface)
        {
            if(empty($template) || (is_string($template) && strpos($template, '.') === false) )
            {
                $identifier         = $this->getIdentifier()->toArray();
                $identifier['path'] = array('template');
                $identifier['name'] = $template;

                $identifier = $this->getIdentifier($identifier);
            }
            else $identifier = $this->getIdentifier($template);

            $template = $identifier;
        }

        $this->__template = $template;

        return $this->__template;
    }

    /**
     * Build an HTML element
     *
     * @param string $tag HTML tag name
     * @param array  $attributes Key/Value pairs for the attributes
     * @param string $children Child elements, not applicable for self-closing tags
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
            'ktml:script', 'ktml:style', 'ktml:include', 'ktml:content', 'ktml:toolbar', 'ktml:wrapper',
        ];

        $attribs = $this->buildAttributes($attributes);
        $attribs = $attribs ? ' '.$attribs : '';
        $tag     = strtolower($tag);

        if (in_array($tag, $self_closing_tags)) {
            return "<$tag$attribs />";
        } else {
            if (is_array($children)) {
                $children = implode("\n", $children);
            }

            return "<$tag$attribs>$children</$tag>";
        }
    }

    /**
     * Build a string with xml style attributes from  an array of key/value pairs
     *
     * @param   mixed   $array The array of Key/Value pairs for the attributes
     * @return  string  String containing xml style attributes
     */
    public function buildAttributes($array)
    {
        $output = array();

        if($array instanceof ObjectConfig) {
            $array = ObjectConfig::unbox($array);
        }

        if(is_array($array))
        {
            foreach($array as $key => $item)
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

                $output[] = $key.'="'.str_replace('"', '&quot;', $item).'"';
            }
        }

        return implode(' ', $output);
    }

    /**
     * Get a template helper
     *
     * @param    mixed $helper ObjectIdentifierInterface
     * @param    array $config An optional associative array of configuration settings
     * @throws  \UnexpectedValueException
     * @return  TemplateHelperInterface
     */
    public function createHelper($helper, $config = array())
    {
        //Create the complete identifier if a partial identifier was passed
        if (is_string($helper) && strpos($helper, '.') === false)
        {
            $identifier = $this->getIdentifier()->toArray();

            if($identifier['type'] != 'lib') {
                $identifier['path'] = array('template', 'helper');
            } else {
                $identifier['path'] = array('helper');
            }

            $identifier['name'] = $helper;
        }
        else $identifier = $this->getIdentifier($helper);

        $config = array_merge(ObjectConfig::unbox($config), ['template' => $this->getTemplate()]);

        return $this->getObject('template.helper.factory')->createHelper($identifier, $config);
    }
}

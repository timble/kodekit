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
 * Decorator Template Filter
 *
 * Replace <ktml:content [decorator="div"]> with the view content allowing to the template to act as a view decorator.
 * If view has no content the <ktml:content> tag will be removed from the template.
 *
 * If additional attribites are defined the content will be wrapped in a div. To specify a different wrapper change the
 * decorator value to the element name.
 *
 * Default attributes can be set through the constructor and will be merged with the specific attributes defined in the
 * <ktml:content> tag.
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Kodekit\Library\Template\Filter
 */
class TemplateFilterDecorator extends TemplateFilterAbstract
{
    /**
     * The decorator attributes
     *
     * @var array
     */
    protected $_attributes;

    /**
     * Constructor.
     *
     * @param ObjectConfig $config An optional ObjectConfig object with configuration options
     */
    public function __construct(ObjectConfig $config)
    {
        parent::__construct($config);

        $this->_attributes = $config->attributes->toArray();
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
            'attributes' => array(),
        ));

        parent::_initialize($config);
    }

    /**
     * Replace <ktml:content> with the view content
     *
     * @param string $text  The text to parse
     * @return void
     */
    public function filter(&$text)
    {
        $matches = array();

        if(preg_match_all('#<ktml:content(.*)>#siU', $text, $matches))
        {
            foreach($matches[0] as $key => $match)
            {
                $attributes = array_merge($this->_attributes,  $this->parseAttributes($matches[1][$key]));

                $content = $this->getTemplate()->content();
                if(!empty($content))
                {
                    //If attributes are set but no decorator set it to <div>
                    $element = null;
                    if(!empty($attributes))
                    {
                        if(isset($attributes['decorator']))
                        {
                            $element = trim(strtolower($attributes['decorator']));
                            unset($attributes['decorator']);
                        }
                        else $element = 'div';
                    }

                    //Do not decorate if no element is defined
                    if($element)
                    {
                        $attribs = '';
                        if(!empty($attributes)) {
                            $attribs = ' '.$this->buildAttributes($attributes);
                        }

                        $content = sprintf('<'.$element.'%s>%s</'.$element.'>', $attribs, PHP_EOL.$content.PHP_EOL);
                    }
                }

                //Remove the tags
                $text = str_replace($match, $content , $text);
            }
        }
    }
}

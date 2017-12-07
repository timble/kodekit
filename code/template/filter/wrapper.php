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
 * Wrapper Template Filter
 *
 * Filter for wrapping a template output
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Kodekit\Library\Template\Filter
 */
class TemplateFilterWrapper extends TemplateFilterAbstract
{
    /**
     * An sprintf parameter with %s in it for the template content
     *
     * @type string
     */
    protected $_wrapper;

    /**
     * @param ObjectConfig $config
     */
    public function __construct(ObjectConfig $config)
    {
        parent::__construct($config);

        $this->setWrapper($config->wrapper);
    }

    /**
     * @param ObjectConfig $config
     */
    protected function _initialize(ObjectConfig $config)
    {
        $config->append(array(
            'priority'  => self::PRIORITY_LOWEST,
            'wrapper' => null
        ));

        parent::_initialize($config);
    }

    /**
     * Checks if the text has <ktml:template:wrapper> to make sure it only runs once
     *
     * @param $text
     * @param TemplateInterface $template A template object.
     * @return void
     */
    public function filter(&$text, TemplateInterface $template)
    {
        if ($this->getWrapper() && strpos($text, '<ktml:template:wrapper>') !== false)
        {
            $text = sprintf($this->getWrapper(), $text);
            $text = str_replace('<ktml:template:wrapper>', '', $text);
        }
    }

    /**
     * @return string
     */
    public function getWrapper()
    {
        return $this->_wrapper;
    }

    /**
     * @param $wrapper
     */
    public function setWrapper($wrapper)
    {
        $this->_wrapper = $wrapper;
    }
}

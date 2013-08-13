<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright	Copyright (C) 2007 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/koowa for the canonical source repository
 */

/**
 * Wrapper Template Filter
 *
 * Filter for wrapping a template output
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Koowa\Library\Template
 */
class KTemplateFilterWrapper extends KTemplateFilterAbstract implements KTemplateFilterWrite
{
    /**
     * An sprintf parameter with %s in it for the template content
     *
     * @type string
     */
    protected $_wrapper;

    /**
     * @param KConfig $config
     */
    public function __construct(KConfig $config)
    {
        parent::__construct($config);

        $this->setWrapper($config->wrapper);
    }

    /**
     * @param KConfig $config
     */
    protected function _initialize(KConfig $config)
    {
        $config->append(array(
            'priority'  => KCommand::PRIORITY_LOWEST,
            'wrapper' => null
        ));

        parent::_initialize($config);
    }

    /**
     * @param $text
     *
     * @return KTemplateFilterWrapper
     */
    public function write(&$text)
    {
        if ($this->getWrapper()) {
            $text = sprintf($this->getWrapper(), $text);
        }

        return $this;
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
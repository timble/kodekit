<?php
/**
* @version      $Id$
* @package      Koowa_Template
* @subpackage   Filter
* @copyright    Copyright (C) 2007 - 2012 Johan Janssens. All rights reserved.
* @license      GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
* @link         http://www.nooku.org
*/

/**
 * Template filter for wrapping a template output
 *
 * @author      Johan Janssens <johan@nooku.org>
 * @package     Koowa_Template
 * @subpackage  Filter
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
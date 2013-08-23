<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright	Copyright (C) 2007 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/koowa for the canonical source repository
 */

/**
 * Alias Template Filter
 *
 * Filter for aliases such as @include, @translate, @helper, @route etc
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Koowa\Library\Template
 */
class KTemplateFilterAlias extends KTemplateFilterAbstract implements KTemplateFilterRead, KTemplateFilterWrite
{
    /**
     * The alias read map
     *
     * @var array
     */
    protected $_alias_read = array(
        '@helper('      => '$this->renderHelper(',
    	'@object('      => '$this->getObject(',
        '@date('        => '$this->renderHelper(\'date.format\',',
        '@overlay('     => '$this->renderHelper(\'behavior.overlay\', ',
        '@translate('   => '$this->translate(',
        '@include('    => '$this->loadIdentifier(',
        '@route('       => '$this->getView()->createRoute(',
        '@escape('      => '$this->getView()->escape(',
    );

    /**
     * The alias write map
     *
     * @var array
     */
    protected $_alias_write = array();

    /**
     * Append an alias
     *
     * @param   array   $alias An array of aliases to be appended
     * @param   integer $mode  Filter mode
     * @return $this
     */
    public function append(array $alias, $mode = KTemplateFilter::MODE_READ)
    {
        if($mode & KTemplateFilter::MODE_READ) {
            $this->_alias_read = array_merge($this->_alias_read, $alias);
        }

        if($mode & KTemplateFilter::MODE_WRITE) {
            $this->_alias_write = array_merge($this->_alias_write, $alias);
        }

        return $this;
    }

    /**
     * Convert the alias
     *
     * @param string
     * @return KTemplateFilterAlias
     */
    public function read(&$text)
    {
        $text = str_replace(
            array_keys($this->_alias_read),
            array_values($this->_alias_read),
            $text);

        return $this;
    }

    /**
     * Convert the alias
     *
     * @param string
     * @return KTemplateFilterAlias
     */
    public function write(&$text)
    {
        $text = str_replace(
            array_keys($this->_alias_write),
            array_values($this->_alias_write),
            $text);

        return $this;
    }
}

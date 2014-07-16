<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright	Copyright (C) 2007 - 2014 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/koowa for the canonical source repository
 */

/**
 * Function Template Filter
 *
 * Compile filter for template functions such as template(), text(), helper(), route() etc
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Koowa\Library\Template
 */
class KTemplateFilterFunction extends KTemplateFilterAbstract implements KTemplateFilterCompiler
{
    /**
     * The functions map.
     *
     * @var array
     */
    protected $_functions = array(
        '@helper('    => '$this->invokeHelper(',
        '@object('    => '$this->getObject(',
        '@date('      => '$this->invokeHelper(\'date.format\',',
        '@overlay('   => '$this->invokeHelper(\'behavior.overlay\', ',
        '@translate(' => '$this->translate(',
        '@import('    => '$this->load(',
        '@route('     => '$this->getView()->getRoute(',
        '@escape('    => '$this->escape(',
        '@title('     => '$this->getView()->getTitle(',
        '@url('       => '$this->getView()->getUrl()->toString(',
    );

    /**
     * Append an alias
     *
     * @param string $name      The function name
     * @param string $rewrite   The function will be rewritten too
     * @return KTemplateFilterFunction
     */
    public function addFunction($name, $rewrite)
    {
        $this->_functions[$name.'('] = $rewrite;
        return $this;
    }

    /**
     * Convert the alias
     *
     * @param string $text  The text to parse
     * @return void
     */
    public function compile(&$text)
    {
        $text = str_replace(
            array_keys($this->_functions),
            array_values($this->_functions),
            $text);
    }
}
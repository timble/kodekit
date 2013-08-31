<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright	Copyright (C) 2007 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/koowa for the canonical source repository
 */

/**
 *  Compiler Template Filter Interface
 *
 * Filter will compile to the template to executable PHP code.
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Koowa\Library\Template
 */
interface KTemplateFilterCompiler
{
    /**
     * Parse the text and compile it to PHP
     *
     * @param string $text  The text to parse
     * @return void
     */
    public function compile(&$text);
}

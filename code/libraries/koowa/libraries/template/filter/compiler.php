<?php
/**
 * Nooku Framework - http://nooku.org/framework
 *
 * @copyright   Copyright (C) 2007 - 2014 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/nooku/nooku-framework for the canonical source repository
 */

/**
 *  Compiler Template Filter Interface
 *
 * Filter will compile to the template to executable PHP code.
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Koowa\Library\Template\Filter
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

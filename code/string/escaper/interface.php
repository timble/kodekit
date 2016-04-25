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
 * String Escaper Interface
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Kodekit\Library\String\Escaper
 */
interface StringEscaperInterface
{
    /**
     * Escapde a string for a specific context
     *
     * @param string $string    The string to escape
     * @param string $context   The context. Default HTML
     * @throws \InvalidArgumentException If the context is not recognised
     * @return string
     */
    public static function escape($string, $context = 'html');
}

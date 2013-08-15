<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright	Copyright (C) 2007 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/koowa for the canonical source repository
 */

/**
 * Word Filter
 *
 * A 'word' is a string containing only the characters [A-Za-z_]
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Koowa\Library\Filter
 */
class KFilterWord extends KFilterAbstract
{
    /**
     * Validate a value
     *
     * @param   mixed  $value Value to be validated
     * @return  bool    True when the variable is valid
     */
    protected function _validate($value)
    {
        $value = trim($value);
        $pattern = '/^[A-Za-z_]*$/';
        return (is_string($value) && preg_match($pattern, $value) == 1);
    }

    /**
     * Sanitize a value
     *
     * @param   mixed  $value Value to be sanitized
     * @return  string
     */
    protected function _sanitize($value)
    {
        $value = trim($value);
        $pattern    = '/[^A-Za-z_]*/';
        return preg_replace($pattern, '', $value);
    }
}

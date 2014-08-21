<?php
/**
 * Nooku Framework - http://nooku.org/framework
 *
 * @copyright   Copyright (C) 2007 - 2014 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/nooku/nooku-framework for the canonical source repository
 */

/**
 * Path Filter
 *
 * Filters Windows and Unix style file paths
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Koowa\Library\Filter
 */
class KFilterPath extends KFilterAbstract implements KFilterTraversable
{
    const PATTERN = '#^(?:[a-z]:/|~*/)[a-z0-9_\.-\s/~]*$#i';

    /**
     * Validate a value
     *
     * @param   mixed  $value Value to be validated
     * @return  bool    True when the variable is valid
     */
    public function validate($value)
    {
        $value = trim(str_replace('\\', '/', $value));
        return (is_string($value) && (preg_match(self::PATTERN, $value)) == 1);
    }

    /**
     * Sanitize a value
     *
     * @param   mixed   $value Value to be sanitized
     * @return  string
     */
    public function sanitize($value)
    {
        $value = trim(str_replace('\\', '/', $value));
        preg_match(self::PATTERN, $value, $matches);
        $match = isset($matches[0]) ? $matches[0] : '';

        return $match;
    }
}

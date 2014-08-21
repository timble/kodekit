<?php
/**
 * Nooku Framework - http://nooku.org/framework
 *
 * @copyright   Copyright (C) 2007 - 2014 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/nooku/nooku-framework for the canonical source repository
 */

/**
 * Command Filter
 *
 * A 'command' is a string containing only the characters [A-Za-z0-9.-_]. Used for names of views, controllers, etc
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Koowa\Library\Filter
 */
class KFilterCmd extends KFilterAbstract implements KFilterTraversable
{
    /**
     * Validate a value
     *
     * @param   mixed   $value Value to be validated
     * @return  bool    True when the variable is valid
     */
    public function validate($value)
    {
        $value = trim($value);
        $pattern = '/^[A-Za-z0-9.\-_]*$/';
        return (is_string($value) && (preg_match($pattern, $value)) == 1);
    }

    /**
     * Sanitize a value
     *
     * @param   mixed   $value Value to be sanitized
     * @return  string
     */
    public function sanitize($value)
    {
        $value = trim($value);
        $pattern    = '/[^A-Za-z0-9.\-_]*/';
        return preg_replace($pattern, '', $value);
    }
}

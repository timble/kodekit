<?php
/**
 * Kodekit - http://timble.net/kodekit
 *
 * @copyright   Copyright (C) 2007 - 2016 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/timble/kodekit for the canonical source repository
 */

namespace Kodekit\Library;

/**
 * MD5 Filter
 *
 * Validates or sanitizes an md5 hash (32 chars [a-f0-9])
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Kodekit\Library\Filter
 */
class FilterMd5 extends FilterAbstract implements FilterTraversable
{
    /**
     * Validate a value
     *
     * @param   mixed  $value Variable to be validated
     * @return  bool    True when the variable is valid
     */
    public function validate($value)
    {
        $value = trim($value);
        $pattern = '/^[a-f0-9]{32}$/';
        return (is_string($value) && preg_match($pattern, $value) == 1);
    }

    /**
     * Sanitize a value
     *
     * @param   mixed  $value Variable to be sanitized
     * @return  string
     */
    public function sanitize($value)
    {
        $value      = trim(strtolower($value));
        $pattern    = '/[^a-f0-9]*/';
        return substr(preg_replace($pattern, '', $value), 0, 32);
    }
}

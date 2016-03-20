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
 * Raw Filter
 *
 * Always validates and returns the raw variable
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Kodekit\Library\Filter
 */
class FilterRaw extends FilterAbstract
{
    /**
     * Validate a value
     *
     * @param   mixed  $value Variable to be validated
     * @return  bool    True when the variable is valid
     */
    public function validate($value)
    {
        return true;
    }

    /**
     * Sanitize a value
     *
     * @param   mixed  $value Variable to be sanitized
     * @return  mixed
     */
    public function sanitize($value)
    {
        return $value;
    }
}

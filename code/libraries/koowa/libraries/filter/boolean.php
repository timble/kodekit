<?php
/**
 * Nooku Framework - http://nooku.org/framework
 *
 * @copyright   Copyright (C) 2007 - 2014 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/nooku/nooku-framework for the canonical source repository
 */

/**
 * Boolean Filter
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Koowa\Library\Filter
 */
class KFilterBoolean extends KFilterAbstract implements KFilterTraversable
{
    /**
     * Validate a value
     *
     *  Returns TRUE for boolean values: "1", "true", "on" and "yes", "0",
     * "false", "off", "no", and "". Returns FALSE for all non-boolean values.
     *
     * @param   mixed   $value Value to be validated
     * @return  bool    True when the variable is valid
     */
    public function validate($value)
    {
        return (null !== filter_var($value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) );
    }

    /**
     * Sanitize a value
     *
     * Returns TRUE for "1", "true", "on" and "yes". Returns FALSE for all other values.
     *
     * @param   mixed   $value Value to be sanitized
     * @return  bool
     */
    public function sanitize($value)
    {
        return (bool) filter_var($value, FILTER_VALIDATE_BOOLEAN);
    }
}

<?php
/**
 * Nooku Framework - http://nooku.org/framework
 *
 * @copyright   Copyright (C) 2007 - 2014 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/nooku/nooku-framework for the canonical source repository
 */

/**
 * Float Filter
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Koowa\Library\Filter
 */
class KFilterFloat extends KFilterAbstract implements KFilterTraversable
{
    /**
     * Validate a value
     *
     * @param   mixed   $value Value to be validated
     * @return  bool    True when the variable is valid
     */
    public function validate($value)
    {
        return (false !== filter_var($value, FILTER_VALIDATE_FLOAT));
    }

    /**
     * Sanitize a value
     *
     * @param   mixed   $value Value to be sanitized
     * @return  float
     */
    public function sanitize($value)
    {
        return (float) filter_var($value, FILTER_SANITIZE_NUMBER_FLOAT,
            FILTER_FLAG_ALLOW_FRACTION | FILTER_FLAG_ALLOW_THOUSAND | FILTER_FLAG_ALLOW_SCIENTIFIC);
    }
}


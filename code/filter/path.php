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
    /**
     * Validate a value
     *
     * @param   mixed  $value Value to be validated
     * @return  bool    True when the variable is valid
     */
    public function validate($value)
    {
        $result = false;

        if (is_string($value) && strlen($value))
        {
            if ($value[0] == '/' || $value[0] == '\\'
                || (strlen($value) > 3 && ctype_alpha($value[0])
                    && $value[1] == ':'
                    && ($value[2] == '\\' || $value[2] == '/')
                )
                || null !== parse_url($value, PHP_URL_SCHEME)
            ) {
                $result = true;
            }
        }

        return $result;
    }

    /**
     * Sanitize a value
     *
     * @param   mixed   $value Value to be sanitized
     * @return  string
     */
    public function sanitize($value)
    {
        return $this->validate($value) ? $value : '';
    }
}

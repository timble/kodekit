<?php
/**
 * Nooku Framework - http://nooku.org/framework
 *
 * @copyright   Copyright (C) 2007 - 2014 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/nooku/nooku-framework for the canonical source repository
 */

/**
 * Html Filter
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Koowa\Component\Koowa\Filter
 */
class ComKoowaFilterHtml extends KFilterAbstract implements KFilterTraversable
{
    /**
     * Validate a value
     *
     * @param   mixed  $value Value to be validated
     * @return  bool    True when the variable is valid
     */
    public function validate($value)
    {
        return is_string($value);
    }

    /**
     * Sanitize a value
     *
     * @param   mixed  $value Input string/array-of-string to be 'cleaned'
     * @return  mixed   'Cleaned' version of input parameter
     */
    public function sanitize($value)
    {
        $value = (string) $value;

        if (!empty($value)) {
            $value = JComponentHelper::filterText($value);
        }

        return $value;
    }
}

<?php
/**
 * Nooku Framework - http://nooku.org/framework
 *
 * @copyright   Copyright (C) 2007 - 2014 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/nooku/nooku-framework for the canonical source repository
 */

/**
 * Alias Filter
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Koowa\Component\Koowa\Filter
 */
class ComKoowaFilterAlias extends KFilterAbstract implements KFilterTraversable
{
    /**
     * Validate a value
     *
     * @param   string  $value Variable to be validated
     * @return  bool    True when the variable is valid
     */
    public function validate($value)
    {
        return true;
    }

    /**
     * Sanitize a value
     *
     * @param   string $value Variable to be sanitized
     * @return  string
     */
    public function sanitize($value)
    {
        return JApplication::stringURLSafe($value);
    }
}

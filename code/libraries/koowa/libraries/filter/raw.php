<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright	Copyright (C) 2007 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/koowa for the canonical source repository
 */

/**
 * Raw filter
 *
 * Always validates and returns the raw variable
 *
 * @author      Johan Janssens <johan@nooku.org>
 * @package     Koowa_Filter
 */
class KFilterRaw extends KFilterAbstract
{
    /**
     * Validate a value
     *
     * @param   scalar  Variable to be validated
     * @return  bool    True when the variable is valid
     */
    protected function _validate($value)
    {
        return true;
    }

    /**
     * Sanitize a value
     *
     * @param   scalar  Variable to be sanitized
     * @return  mixed
     */
    protected function _sanitize($value)
    {
        return $value;
    }
}

<?php
/**
 * Nooku Framework - http://nooku.org/framework
 *
 * @copyright   Copyright (C) 2007 - 2014 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/nooku/nooku-framework for the canonical source repository
 */

/**
 * Internal Url Filter
 *
 * Check if an refers to a legal URL inside the system. Use when redirecting to an URL that was passed in a request
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Koowa\Library\Filter
 */
class KFilterInternalurl extends KFilterAbstract implements KFilterTraversable
{
    /**
     * Validate a value
     *
     * @param   mixed $value Value to be validated
     * @return  bool    True when the variable is valid
     */
    public function validate($value)
    {
        if(!is_string($value)) {
            return false;
        }

        if(stripos($value, (string)  $this->getObject('request')->getUrl()->toString(KHttpUrl::SCHEME | KHttpUrl::HOST)) !== 0) {
            return false;
        }

        return true;
    }

    /**
     * Sanitize a value
     *
     * @param   mixed $value Value to be sanitized
     * @return  string
     */
    public function sanitize($value)
    {
        //TODO : internal URLs should not only have path and query information
        return filter_var($value, FILTER_SANITIZE_URL);
    }
}


<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright	Copyright (C) 2007 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/koowa for the canonical source repository
 */

/**
 * Json Filter
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Koowa\Library\Filter
 */
class KFilterJson extends KFilterAbstract
{
    /**
     * Constructor
     *
     * @param   KConfig $config Configuration options
     */
    public function __construct(KConfig $config)
    {
        parent::__construct($config);

        //Don't walk the incoming data array or object
        $this->_walk = false;
    }

    /**
     * Validate a value
     *
     * @param   scalar  Value to be validated
     * @return  bool    True when the variable is valid
     */
    protected function _validate($value)
    {
        return is_string($value) && !is_null(json_decode($value));
    }

    /**
     * Sanitize a value
     *
     * The value passed will be encoded to JSON format.
     *
     * @param   scalar  Value to be sanitized
     * @return  string
     */
    protected function _sanitize($value)
    {
        // If instance of KConfig casting to string will make it encode itself to JSON
        if($value instanceof KConfig) {
            $result = (string) $value;
        }
        else
        {
            //Don't re-encode if the value is already in json format
            if(is_string($value) && (json_decode($value) !== NULL)) {
                $result = $value;
            } else {
                $result = json_encode($value);
            }
        }

        return $result;
    }
}

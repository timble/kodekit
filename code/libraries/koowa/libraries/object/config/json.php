<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright	Copyright (C) 2007 - 2014 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/koowa for the canonical source repository
 */

/**
 * Object Config Json
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Koowa\Library\Object
 */
class KObjectConfigJson extends KObjectConfigFormat
{
    /**
     * Read from a string and create an array
     *
     * @param  string $string
     * @throws DomainException  If the JSON cannot be decoded or if the encoded data is deeper than the recursion limit.
     * @return KObjectConfigJson
     */
    public function fromString($string)
    {
        $data = array();

        if(!empty($string))
        {
            $data = json_decode($string, true);

            if($data === null) {
                throw new DomainException('Cannot decode data from JSON string');
            }
        }

        $this->merge($data);

        return $this;
    }

    /**
     * Write a config object to a string.
     *
     * @return string|false    Returns a JSON encoded string on success. False on failure.
     * @throws DomainException Object could not be encoded to valid JSON.
     */
    public function toString()
    {
        $data = $this->toArray();
        $data = json_encode($data);

        if($data === false) {
            throw new DomainException('Cannot encode data to JSON string');
        }

        return $data;
    }
}
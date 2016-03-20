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
 * Object Config Json
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Kodekit\Library\Object\Config
 */
class ObjectConfigJson extends ObjectConfigFormat
{
    /**
     * Read from a string and create an array
     *
     * @param  string   $string
     * @param  bool     $object  If TRUE return a ObjectConfig, if FALSE return an array. Default TRUE.
     * @throws \DomainException  If the JSON cannot be decoded or if the encoded data is deeper than the recursion limit.
     * @return ObjectConfigJson|array
     */
    public function fromString($string, $object = true)
    {
        $data = array();

        if(!empty($string))
        {
            $data = json_decode($string, true);

            if (json_last_error() > 0) {
                throw new \DomainException(sprintf('Cannot decode from JSON string - %s', json_last_error_msg()));
            }
        }

        return $object ? $this->merge($data) : $data;
    }

    /**
     * Write a config object to a string.
     *
     * @return string|false    Returns a JSON encoded string on success. False on failure.
     * @throws \DomainException Object could not be encoded to valid JSON.
     */
    public function toString()
    {
        $data = $this->toArray();
        $data = json_encode($data);

        if (json_last_error() > 0) {
            throw new \DomainException(sprintf('Cannot encode data to JSON string - %s', json_last_error_msg()));
        }

        return $data;
    }
}
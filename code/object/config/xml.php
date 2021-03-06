<?php
/**
 * Kodekit - http://timble.net/kodekit
 *
 * @copyright   Copyright (C) 2007 - 2016 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license     MPL v2.0 <https://www.mozilla.org/en-US/MPL/2.0>
 * @link        https://github.com/timble/kodekit for the canonical source repository
 */

namespace Kodekit\Library;

/**
 * Object Config Xml
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Kodekit\Library\Object\Config
 */
class ObjectConfigXml extends ObjectConfigFormat
{
    /**
     * The format
     *
     * @var string
     */
    protected static $_format = 'application/xml';

    /**
     * Read from a string and create an array
     *
     * @param  string   $string
     * @param  bool     $object  If TRUE return a ObjectConfig, if FALSE return an array. Default TRUE.
     * @throws \DomainException
     * @return ObjectConfigXml|array
     */
    public function fromString($string, $object = true)
    {
        $data = array();

        if(!empty($string))
        {
            $xml  = simplexml_load_string($string);

            if($xml === false) {
                throw new \DomainException('Cannot parse XML string');
            }

            foreach ($xml->children() as $node) {
                $data[(string) $node['name']] = self::_decodeValue($node);
            }
        }

        return $object ? $this->merge($data) : $data;
    }

    /**
     * Write a config object to a string.
     *
     * @return string|false   Returns a XML encoded string on success. False on failure.
     */
    public function toString()
    {
        $addChildren = function($value, $key, $node)
        {
            if (is_scalar($value))
            {
                $n = $node->addChild('option', $value);
                $n->addAttribute('name', $key);
                $n->addAttribute('type', gettype($value));
            }
            else
            {
                $n = $node->addChild('config');
                $n->addAttribute('name', $key);
                $n->addAttribute('type', gettype($value));

                array_walk($value, $addChildren, $n);
            }
        };

        $xml  = simplexml_load_string('<config />');
        $data = $this->toArray();
        array_walk($data, $addChildren, $xml);

        return $xml->asXML();
    }

    /**
     * Method to get a PHP native value for a SimpleXMLElement object
     *
     * @param   object  $node  SimpleXMLElement object for which to get the native value.
     * @return  mixed  Native value of the SimpleXMLElement object.
     */
    protected static function _decodeValue($node)
    {
        switch ($node['type'])
        {
            case 'integer':
                $value = (string) $node;
                return (int) $value;
                break;

            case 'string':
                return (string) $node;
                break;

            case 'boolean':
                $value = (string) $node;
                return (bool) $value;
                break;

            case 'double':
                $value = (string) $node;
                return (float) $value;
                break;

            case 'array':
            default     :

                $value = array();
                foreach ($node->children() as $child) {
                    $value[(string) $child['name']] = self::_decodeValue($child);
                }

                break;
        }

        return $value;
    }
}
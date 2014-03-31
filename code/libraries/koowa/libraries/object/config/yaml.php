<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright	Copyright (C) 2007 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/koowa for the canonical source repository
 */

/**
 * Object Config Yaml
 *
 * @author  Johan Janssens <http://nooku.assembla.com/profile/johanjanssens>
 * @package Koowa\Library\Object
 */
class KObjectConfigYaml extends KObjectConfigFormat
{
    /**
     * Read from a YAML string and create a config object
     *
     * @param  string $string
     * @return $this
     * @throws \RuntimeException
     */
    public function fromString($string)
    {
        if(function_exists('yaml_parse'))
        {
            $data = array();

            if(!empty($string))
            {
                $data = yaml_parse($string);

                if($data === false) {
                    throw new RuntimeException('Cannot parse YAML string');
                }
            }

            $this->add($data);
        }

        return $this;
    }

    /**
     * Write a config object to a YAML string.
     *
     * @return string|false     Returns a YAML encoded string on success. False on failure.
     */
    public function toString()
    {
        $result = false;

        if(function_exists('yaml_emit'))
        {
            $data   = $this->toArray();
            $result = yaml_emit($data, YAML_UTF8_ENCODING);
        }

        return $result;
    }
}
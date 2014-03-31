<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright	Copyright (C) 2007 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/koowa for the canonical source repository
 */

/**
 * Object Config Json
 *
 * @author  Johan Janssens <http://nooku.assembla.com/profile/johanjanssens>
 * @package Koowa\Library\Object
 */
class KObjectConfigJson extends KObjectConfigFormat
{
    /**
     * Read from a string and create an array
     *
     * @param  string $string
     * @return $this
     * @throws \RuntimeException
     */
    public function fromString($string)
    {
        $data = array();

        if(!empty($string))
        {
            $data = json_decode($string, true);

            if($data === null) {
                throw new RuntimeException('Cannot decode JSON string');
            }
        }

        $this->add($data);

        return $this;
    }

    /**
     * Write a config object to a string.
     *
     * @return string|false     Returns a JSON encoded string on success. False on failure.
     */
    public function toString()
    {
        $data = $this->toArray();

        return json_encode($data);
    }
}
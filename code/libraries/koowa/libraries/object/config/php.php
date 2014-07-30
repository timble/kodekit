<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright	Copyright (C) 2007 - 2014 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/koowa for the canonical source repository
 */

/**
 * Object Config Php
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Koowa\Library\Object
 */
class KObjectConfigPhp extends KObjectConfigFormat
{
    /**
     * Read from a string and create an array
     *
     * @param  string $string
     * @throws DomainException
     * @return KObjectConfigPhp
     */
    public function fromString($string)
    {
        $data = array();

        if(!empty($string))
        {
            $data = eval($string);

            if($data === false) {
                throw new DomainException('Cannot evaluate data from PHP string');
            }
        }

        $this->add($data);

        return $this;
    }

    /**
     * Write a config object to a string.
     *
     * @return string|false   Returns a parsable string representation of the data.. False on failure.
     */
    public function toString()
    {
        $data = $this->toArray();

        return '<?php return '.var_export($data, true).';';
    }

    /**
     * Read from a file and create a config object
     *
     * @param  string $filename
     * @throws RuntimeException
     * @return KObjectConfigPhp
     */
    public function fromFile($filename)
    {
        if (!is_file($filename) || !is_readable($filename)) {
            throw new RuntimeException(sprintf("File '%s' doesn't exist or not readable", $filename));
        }

        $this->add(include $filename);

        return $this;
    }
}
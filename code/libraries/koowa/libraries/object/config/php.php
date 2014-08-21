<?php
/**
 * Nooku Framework - http://nooku.org/framework
 *
 * @copyright   Copyright (C) 2007 - 2014 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/nooku/nooku-framework for the canonical source repository
 */

/**
 * Object Config Php
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Koowa\Library\Object\Config
 */
class KObjectConfigPhp extends KObjectConfigFormat
{
    /**
     * Read from a string and create an array
     *
     * @param  string $string
     * @param  bool    $object  If TRUE return a ConfigObject, if FALSE return an array. Default TRUE.
     * @throws DomainException
     * @return KObjectConfigPhp|array
     */
    public function fromString($string, $object = true)
    {
        $data = array();

        if(!empty($string))
        {
            $data = eval($string);

            if($data === false) {
                throw new DomainException('Cannot evaluate data from PHP string');
            }
        }

        return $object ? $this->merge($data) : $data;
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
     * @param  bool    $object  If TRUE return a ConfigObject, if FALSE return an array. Default TRUE.
     * @throws RuntimeException If file doesn't exist is not readable or cannot be included.
     * @return KObjectConfigPhp|array
     */
    public function fromFile($filename, $object = true)
    {
        if (!is_file($filename) || !is_readable($filename)) {
            throw new RuntimeException(sprintf("File '%s' doesn't exist or not readable", $filename));
        }

        $data = $this->_includeFile($filename);

        return $object ? $this->merge($data) : $data;
    }

    /**
     * Includes a file without exposing caller method's scope
     *
     * @param  string $filename
     * @return mixed
     */
    protected function _includeFile($filename)
    {
        return require $filename;
    }
}
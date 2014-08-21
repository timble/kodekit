<?php
/**
 * Nooku Framework - http://nooku.org/framework
 *
 * @copyright   Copyright (C) 2007 - 2014 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/nooku/nooku-framework for the canonical source repository
 */

/**
 * Object Config Serializable Interface
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Koowa\Library\Object\Config
 */
interface KObjectConfigSerializable
{
    /**
     * Read from a string and create a ObjectConfig object
     *
     * @param  string $string
     * @param  bool    $object  If TRUE return a ConfigObject, if FALSE return an array. Default TRUE.
     * @throws DomainException
     * @return KObjectConfigSerializable|array
     */
    public function fromString($string, $object = false);

    /**
     * Write a config object to a string.
     *
     * @return string
     */
    public function toString();

    /**
     * Read from a file and create an array
     *
     * @param  string $filename
     * @param  bool    $object  If TRUE return a ConfigObject, if FALSE return an array. Default TRUE.
     * @throws RuntimeException
     * @return KObjectConfigSerializable|array
     */
    public function fromFile($filename, $object = false);

    /**
     * Write a config object to a file.
     *
     * @param  string  $filename
     * @return KObjectConfigSerializable
     */
    public function toFile($filename);
}
<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright	Copyright (C) 2007 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/koowa for the canonical source repository
 */

/**
 * Object Config Serializable Interface
 *
 * @author  Johan Janssens <http://nooku.assembla.com/profile/johanjanssens>
 * @package Koowa\Library\Object
 */
interface KObjectConfigSerializable
{
    /**
     * Read from a string and create a ObjectConfig object
     *
     * @param  string $string
     * @return $this
     * @throws \RuntimeException
     */
    public function fromString($string);

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
     * @return $this
     * @throws \RuntimeException
     */
    public function fromFile($filename);

    /**
     * Write a config object to a file.
     *
     * @param  string  $filename
     * @return void
     */
    public function toFile($filename);
}
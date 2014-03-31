<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright	Copyright (C) 2007 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/koowa for the canonical source repository
 */

/**
 * Abstract Object Config Format
 *
 * @author  Johan Janssens <http://nooku.assembla.com/profile/johanjanssens>
 * @package Koowa\Library\Object
 */
abstract class KObjectConfigFormat extends KObjectConfig implements KObjectConfigSerializable
{
    /**
     * Read from a file and create a config object
     *
     * @param  string $filename
     * @return $this
     * @throws \RuntimeException
     */
    public function fromFile($filename)
    {
        if (!is_file($filename) || !is_readable($filename)) {
            throw new RuntimeException(sprintf("File '%s' doesn't exist or not readable", $filename));
        }

        $string = file_get_contents($filename);
        $this->fromString($string);

        return $this;
    }

    /**
     * Write a config object to a file.
     *
     * @param  string  $filename
     * @return void
     * @throws \InvalidArgumentException
     * @throws \RuntimeException
     */
    public function toFile($filename)
    {
        $directory = dirname($filename);

        if(empty($filename)) {
            throw new InvalidArgumentException('No file name specified');
        }

        if (!is_dir($directory)) {
            throw new RuntimeException(sprintf('Directory : %s does not exists!', $directory));
        }

        if (!is_writable($directory)) {
            throw new RuntimeException(sprintf("Cannot write in directory : %s", $directory));
        }

        //Try to write the file
        $result = file_put_contents($filename, $this->toString(), LOCK_EX);

        if($result === false) {
            throw new RuntimeException(sprintf("Error writing to %s", $filename));
        }
    }

    /**
     * Allow PHP casting of this object
     *
     * @return string
     */
    public function __toString()
    {
        return $this->toString();
    }
}
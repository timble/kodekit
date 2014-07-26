<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright	Copyright (C) 2007 - 2014 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/koowa for the canonical source repository
 */

/**
 * FileSystem Stream Wrapper Interface
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Koowa\Library\Filesystem
 */
interface KFilesystemStreamWrapperInterface extends KObjectInterface, KObjectMultiton
{
    /**
     * Get the stream wrapper name used to register the stream with
     *
     * @return string The stream wrapper name
     */
    public static function getName();

    /**
     * Get the stream type
     *
     * @return string The stream type
     */
    public function getType();

    /**
     * Get the stream path
     *
     * @return string The stream path
     */
    public function getPath();

    /**
     * Set the stream options
     *
     * @return string The stream options
     */
    public function getOptions();

    /**
     * Set the stream options
     *
     * @param string $options Set the stream options
     */
    public function setOptions($options);

    /**
     * Set the stream mode
     *
     * @return string The stream mode
     */
    public function getMode();

    /**
     * Set the stream mode
     *
     * @param $mode The stream mode
     */
    public function setMode($mode);
}
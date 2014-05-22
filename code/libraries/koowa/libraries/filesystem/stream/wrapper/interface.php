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
     * Register the stream wrapper
     *
     * @return bool
     */
    public function register();

    /**
     * Un Register the stream wrapper
     *
     * @return bool
     */
    public function unregister();

    /**
     * Check if the stream wrapper is registered
     *
     * @return bool TRUE if the path is a registered stream URL, FALSE otherwise.
     */
    public function isRegistered();

    /**
     * Get the stream type
     *
     * @return string The stream type
     */
    public function getType();

    /**
     * Get the stream protocol used to register the stream with
     *
     * @return string The stream protocol
     */
    public function getProtocol();

    /**
     * Get the stream path
     *
     * @return string The stream protocol
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
     * @param $mode
     */
    public function setMode($mode);
}
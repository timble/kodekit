<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright	Copyright (C) 2007 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/koowa for the canonical source repository
 */

/**
 * Component Template Locator
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Koowa\Library\Template
 */
class KTemplateLocatorComponent extends KTemplateLocatorAbstract
{
    /**
     * Locate the template based on a virtual path
     *
     * @param  string $path  Stream path or resource
     * @return string   The physical stream path for the template
     */
    public function locate($path)
    {
        $path = $this->_getPath($path);

        // Find the template
        $result = $this->realPath($path);

        return $result;
    }

    /**
     * Converts a path identifier into a full path
     *
     * @param  string $path
     * @return string File path
     */
    protected function _getPath($path)
    {
        // Make sure we have a proper identifier
        if (is_string($path) && strpos($path, '.') === false)
        {
            $identifier = clone $this->getTemplate()->getView()->getIdentifier();
            $identifier->name = $path;
        }
        else $identifier = $path;

        //Identify the template
        $identifier = $this->getIdentifier($identifier);

        $path = dirname($identifier->filepath).'/'.$identifier->name.'.php';

        return $path;
    }

    /**
     * Get a path from an file
     *
     * Function will check if the path is an alias and return the real file path
     *
     * @param  string $file The file path
     * @return string The real file path
     */
    public function realPath($file)
    {
        $result = false;
        $path   = dirname($file);

        // Is the path based on a stream?
        if (strpos($path, '://') === false)
        {
            // Not a stream, so do a realpath() to avoid directory traversal attempts on the local file system.
            $path = realpath($path); // needed for substr() later
            $file = realpath($file);
        }

        // The substr() check added to make sure that the realpath() results in a directory registered so that
        // non-registered directories are not accessible via directory traversal attempts.
        if (file_exists($file) && substr($file, 0, strlen($path)) == $path) {
            $result = $file;
        }

        return $result;
    }
}
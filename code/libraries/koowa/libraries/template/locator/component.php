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
     * @return string The physical stream path for the template
     */
    public function locate($path)
    {
        $result = false;
        $info   = pathinfo( $path );

        //Handle short hand identifiers
        if(strpos($info['filename'], '.') === false)
        {
            $path       = pathinfo($this->getTemplate()->getPath());
            $identifier = $this->getIdentifier($path['filename']);
            $filename   = $info['filename'];
        }
        else
        {
            $identifier = $this->getIdentifier($info['dirname'].'/'.$info['filename']);
            $filename   = $identifier->name;
        }

        $parts = $identifier->path;

        if($parts[0] === 'view') {
            $parts[0] = KStringInflector::pluralize($parts[0]);
        }

        $component = 'com_'.strtolower($identifier->package);
        $extension = $info['extension'].'.php';

        $basepath  = $identifier->basepath.'/components/'.$component;
        $filepath  = implode('/', $parts).'/tmpl';
        $fullpath  = $basepath.'/'.$filepath.'/'.$filename.'.'.$extension;

        // Find the template
        $result = $this->realPath($fullpath);

        return $result;
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
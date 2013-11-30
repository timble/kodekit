<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright	Copyright (C) 2007 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/koowa for the canonical source repository
 */

/**
 * Module Template Locator
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Koowa\Module\Koowa
 */
class ModKoowaTemplateLocatorModule extends KTemplateLocatorAbstract
{
    /**
     * Locate the template based on a virtual path
     *
     * @param  string $path Stream path or resource
     * @throws RuntimeException
     * @return string The physical stream path for the template
     */
    public function locate($path)
    {
        if(strpos($path, ':') === false)
        {
            if(!$base = $this->getTemplate()->getPath()) {
                throw new RuntimeException('Cannot qualify partial template path');
            }

            $identifier = clone $this->getIdentifier($base);

            $format    = pathinfo($path, PATHINFO_EXTENSION);
            $template  = pathinfo($path, PATHINFO_FILENAME);

            $parts     = $identifier->path;
            array_pop($parts);
        }
        else
        {
            // Need to clone here since we use array_pop and it modifies the cached identifier
            $identifier = clone $this->getIdentifier($path);

            $format    = $identifier->name;
            $template  = array_pop($identifier->path);
            $parts     = $identifier->path;
        }

        $basepath  = $identifier->basepath.'/modules/mod_'.strtolower($identifier->package);
        $filepath  = (count($parts) ? implode('/', $parts).'/' : '').'tmpl';
        $fullpath  = $basepath.'/'.$filepath.'/'.$template.'.'.$format.'.php';

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
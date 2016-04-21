<?php
/**
 * Kodekit - http://timble.net/kodekit
 *
 * @copyright   Copyright (C) 2007 - 2016 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license     MPL v2.0 <https://www.mozilla.org/en-US/MPL/2.0>
 * @link        https://github.com/timble/kodekit for the canonical source repository
 */

namespace Kodekit\Library;

/**
 * Locator Filesystem Interface
 *
 * @author  Johan Janssens <http://github.com/johanjanssens>
 * @package Kodekit\Library\Filesystem\Locator
 */
interface FilesystemLocatorInterface
{
    /**
     * Get the locator name
     *
     * @return string The locator name
     */
    public static function getName();

    /**
     * Locate the translation based on a physical path
     *
     * @param  string $url       The resource url
     * @return string|false  The physical file path for the resource or FALSE if the url cannot be located
     */
    public function locate($url);

    /**
     * Parse the resource url
     *
     * @param  string $url The resource url
     * @return array
     */
    public function parseUrl($url);

    /**
     * Register a path template
     *
     * @param  string $template   The path template
     * @param  bool $prepend      If true, the template will be prepended instead of appended.
     * @return FilesystemLocatorInterface
     */
    public function registerPathTemplate($template, $prepend = false);

    /**
     * Get the list of path templates
     *
     * @param  string $url   The resource url
     * @return array The path templates
     */
    public function getPathTemplates($url);

    /**
     * Get a path from an file
     *
     * Function will check if the path is an alias and return the real file path
     *
     * @param  string $file The file path
     * @return string The real file path
     */
    public function realPath($file);

    /**
     * Returns true if the resource is still fresh.
     *
     * @param  string $url    The resource url
     * @param int     $time   The last modification time of the cached resource (timestamp)
     * @return bool TRUE if the resource is still fresh, FALSE otherwise
     */
    public function isFresh($url, $time);
}

<?php
/**
 * Nooku Framework - http://nooku.org/framework
 *
 * @copyright   Copyright (C) 2007 - 2014 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/nooku/nooku-framework for the canonical source repository
 */

/**
 * Template Locator Interface
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Koowa\Library\Template\Locator
 */
interface KTemplateLocatorInterface
{
    /**
     * Get the locator name
     *
     * @return string The stream name
     */
    public static function getName();

    /**
     * Sets the base path
     *
     * @param string $path  The path (used to resolved partials).
     * @return KTemplateLocatorInterface
     */
    public function setBasePath($path);

    /**
     * Get the path
     *
     * @return string|null
     */
    public function getBasePath();

    /**
     * Find the template path
     *
     * @param  string $url   The Template url
     * @throws RuntimeException If the no base path exists while trying to locate a partial.
     * @return string|false The real template path or FALSE if the template could not be found
     */
    public function locate($url);

    /**
     * Find a template path
     *
     * @param array  $info The path information
     * @return string|false The real template path or FALSE if the template could not be found
     */
    public function find(array $info);

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
     * Returns true if the template is still fresh.
     *
     * @param  string $url   The Template url
     * @param int     $time  The last modification time of the cached template (timestamp)
     * @return bool TRUE if the template is still fresh, FALSE otherwise
     */
    public function isFresh($url, $time);
}
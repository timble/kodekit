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
 * Template Engine Interface
 *
 * @author  Johan Janssens <http://github.com/johanjanssens>
 * @package Kodekit\Library\Template\Engine
 */
interface TemplateEngineInterface extends TemplateInterface
{
    /**
     * Get the engine supported file types
     *
     * @return array
     */
    public static function getFileTypes();

    /**
     * Render a partial template
     *
     * This method merges the data passed in with the data from the parent template. If the partial template
     * has different file type the method will try to allocate it by jumping out of the local template scope.
     *
     * @param   string  $url      The template url
     * @param   array   $data     The data to pass to the template
     * @throws \RuntimeException  If a partial template url could not be fully qualified
     * @return  string The rendered template content
     */
    public function renderPartial($url, array $data = array());

    /**
     * Render debug information
     *
     * @param  string  $source  The template source
     * @return string The rendered template source
     */
    public function renderDebug($source);

    /**
     * Locate a template source from form a url
     *
     * @param   string  $url The template source url
     * @throws \InvalidArgumentException If the template could not be located
     * @throws \RuntimeException If a partial template url could not be fully qualified
     * @return string  The template real path
     */
    public function locateSource($url);

    /**
     * Cache the template source to a file
     *
     * Write the template source to a file cache. Requires cache to be enabled. This method will throw exceptions if
     * caching fails and debug is enabled. If debug is disabled FALSE will be returned.
     *
     * @param  string $name   The file name
     * @param  string $source  The template source
     * @throws \RuntimeException If the file path does not exist
     * @throws \RuntimeException If the file path is not writable
     * @throws \RuntimeException If template cannot be written to the cache
     * @return bool TRUE on success. FALSE on failure
     */
    public function cacheSource($name, $source);

    /**
     * Enable or disable engine debugging
     *
     * If debug is enabled the engine will throw an exception if caching fails.
     *
     * @param bool $debug True or false.
     * @return TemplateEngineInterface
     */
    public function setDebug($debug);

    /**
     * Check if the engine is running in debug mode
     *
     * @return bool
     */
    public function isDebug();

    /**
     * Enable or disable the cache
     *
     * @param bool   $cache True or false.
     * @param string $path  The cache path
     * @param bool   $reload
     * @return TemplateEngineAbstract
     */
    public function setCache($cache, $path, $reload = true);

    /**
     * Check if caching is enabled
     *
     * @return bool
     */
    public function isCache();

    /**
     * Check if a file exists in the cache
     *
     * @param string $file The file name
     * @return string|false The cache file path. FALSE if the file cannot be found in the cache
     */
    public function isCached($file);
}
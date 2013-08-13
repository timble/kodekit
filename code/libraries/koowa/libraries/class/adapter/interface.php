<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright	Copyright (C) 2007 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/koowa for the canonical source repository
 */

/**
 * Loader Adapter Interface
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Koowa\Library\Loader
 */
interface KClassAdapterInterface
{
    /**
	 * Get the type
	 *
	 * @return string	Returns the type
	 */
	public function getType();

	/**
	 * Get the class prefix
	 *
	 * @return string	Returns the class prefix
	 */
	public function getPrefix();

    /**
     * Register a specific package basepath
     *
     * @param  string   $basepath The base path of the package
     * @param  string   $package
     * @return KClassAdapterInterface
     */
    public function registerBasepath($basepath, $package = null);

    /**
     * Get the registered base paths
     *
     * @return array An array with package name as keys and base path as values
     */
    public function getBasepaths();

    /**
     * Get the path based on a class name
     *
     * @param  string  $classname The class name
     * @param  string  $basepath  The basepath to use to find the class
     * @return string|boolean     Returns the path on success FALSE on failure
     */
    public function findPath($classname, $basepath = null);
}

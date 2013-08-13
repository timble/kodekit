<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright	Copyright (C) 2007 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/koowa for the canonical source repository
 */

/**
 * Class Loader Interface
 *
 * @author  Johan Janssens <http://nooku.assembla.com/profile/johanjanssens>
 * @package Nooku\Library\Class
 */
interface KClassLoaderInterface
{
    /**
     * Registers the loader with the PHP autoloader.
     *
     * @param Boolean $prepend Whether to prepend the autoloader or not
     * @see \spl_autoload_register();
     */
    public function register($prepend = false);

    /**
     * Unregisters the loader with the PHP autoloader.
     *
     * @see \spl_autoload_unregister();
     */
    public function unregister();

    /**
     * Register a class locator
     *
     * @param KClassLocatorInterface $locator
     * @return void
     */
    public function registerLocator(KClassLocatorInterface $locator);

    /**
     * Get a registered class locator based on his type
     *
     * @param string $type The locator type
     * @return KClassLocatorInterface|null  Returns the object locator or NULL if it cannot be found.
     */
    public function getLocator($type);

    /**
     * Get the registered adapters
     *
     * @return array
     */
    public function getLocators();

    /**
     * Load a class based on a class name
     *
     * @param  string   $class  The class name
     * @return boolean  Returns TRUE if the class could be loaded, otherwise returns FALSE.
     */
    public function loadClass($class);

    /**
     * Load a class based on a path
     *
     * @param string	$path The file path
     * @return boolean  Returns TRUE if the file could be loaded, otherwise returns FALSE.
     */
    public function loadFile($path);

    /**
     * Get the path based on a class name
     *
     * @param string $class   The class name
     * @return string|boolean   Returns canonicalized absolute pathname or FALSE of the class could not be found.
     */
    public function findPath($class);

    /**
     * Tells if a class, interface or trait exists.
     *
     * @params string $class
     * @return boolean
     */
    public function isDeclared($class);
}
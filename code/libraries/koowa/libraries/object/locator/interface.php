<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright	Copyright (C) 2007 - 2014 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/koowa for the canonical source repository
 */

/**
 * Object Locator Interface
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Koowa\Library\Object
 */
interface KObjectLocatorInterface
{
    /**
     * Returns a fully qualified class name for a given identifier.
     *
     * @param KObjectIdentifier $identifier An identifier object
     * @param bool  $fallback   Use the fallback sequence to locate the identifier
     * @return string|false  Return the class name on success, returns FALSE on failure
     */
    public function locate(KObjectIdentifier $identifier, $fallback = true);

    /**
     * Find a class
     *
     * @param array  $info      The class information
     * @param bool   $fallback  If TRUE use the fallback sequence
     * @return bool|mixed
     */
    public function find(array $info, $fallback = true);

    /**
     * Register a package
     *
     * @param  string $name    The package name
     * @param  string $domain  The domain for the package
     * @return KObjectLocatorInterface
     */
    public function registerPackage($name, $domain);

    /**
     * Get the registered package domain
     *
     *  * If no domain has been registered for this package, the default 'Koowa' domain will be returned.
     *
     * @param string $package
     * @return string The registered domain
     */
    public function getPackage($package);

    /**
     * Get the registered packages
     *
     * @return array An array with package names as keys and domain names as values
     */
    public function getPackages();

    /**
     * Get the locator type
     *
     * @return string
     */
    public function getType();

    /**
     * Get the locator fallback sequence
     *
     * @return array
     */
    public function getSequence();
}

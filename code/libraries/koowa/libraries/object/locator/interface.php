<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright	Copyright (C) 2007 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
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
     * @param bool  $fallback   Use the fallbacks to locate the identifier
     * @return string|false  Return the class name on success, returns FALSE on failure
     */
    public function locate(KObjectIdentifier $identifier, $fallback = true);

    /**
     * Find a class
     *
     * @param array  $info      The class information
     * @param string $basepath  The basepath name
     * @param bool   $fallback  If TRUE use the fallback sequence
     * @return bool|mixed
     */
    public function find(array $info, $basepath = null, $fallback = true);

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

    /**
     * Get the class loader
     *
     * @return KClassLoaderInterface
     */
    public function getClassLoader();

    /**
     * Set the class loader
     *
     * @param KClassLoaderInterface $loader
     * @return KObjectManagerInterface
     */
    public function setClassLoader(KClassLoaderInterface $loader);
}
